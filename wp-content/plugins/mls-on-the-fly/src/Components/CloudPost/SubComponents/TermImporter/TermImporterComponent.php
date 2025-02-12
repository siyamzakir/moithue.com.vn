<?php

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\TermImporter;

use Realtyna\Core\Abstracts\ComponentAbstract;
use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Boot\Log;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Interfaces\IntegrationInterface;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Mapping\Mapping;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Entities\RFTermEntity;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\Exceptions\EntityNotDefinedException;
use Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\RFClient\SDK\RF\RF;
use Realtyna\MlsOnTheFly\Settings\Settings;
use WP_Error;
use WP_Term_Query;

class TermImporterComponent extends ComponentAbstract
{

    private Mapping $mapping;
    private IntegrationInterface $integration;
    private RF $RF;

    /**
     * @throws \ReflectionException
     */
    public function register(): void
    {
        $this->mapping = App::get(Mapping::class);
        $this->integration = App::get(IntegrationInterface::class);
        $this->RF = App::get(RF::class);

        add_filter('cron_schedules', function ($schedules) {
            $schedules['every_minute'] = [
                'interval' => 60,
                'display' => esc_html__('Every Minute'),
            ];
            return $schedules;
        });

        add_action('wp', function () {
            if (!wp_next_scheduled('realtyna_update_terms_hook')) {
                wp_schedule_event(time(), 'every_minute', 'realtyna_update_terms_hook');
            }
        });

        add_action('realtyna_update_terms_hook', [$this, 'updateTerms']);
    }

    /**
     * Update terms in custom taxonomies based on mapping from RF queries.
     * @throws EntityNotDefinedException
     */
    function updateTerms(): bool
    {
        $taxonomies = $this->integration->customTaxonomies;
        foreach ($taxonomies as $taxonomy) {
            $this->updateTaxonomy($taxonomy);
        }

        return true;
    }


    /**
     * @throws EntityNotDefinedException
     */
    private function updateTaxonomy($taxonomy): void
    {
        if (!taxonomy_exists($taxonomy)) {
            Log::warning('Taxonomy does not exist', ['taxonomy' => $taxonomy]);
            return;
        }

        if (!$this->isTaxonomyUpdateScheduled($taxonomy)) {
            $this->performUpdateProcess($taxonomy);
        }
    }

    /**
     * Check if the taxonomy update is scheduled.
     */
    function isTaxonomyUpdateScheduled(string $taxonomy): bool
    {
        $last_update_time = get_option('realtyna_mls_on_the_fly_taxonomy_last_update_time_' . $taxonomy);
        if ($last_update_time && time() - $last_update_time < 172800) { // Two days
            return true;
        }
        return false;
    }

    /**
     * Perform the update process for the taxonomy.
     * @throws EntityNotDefinedException
     */
    function performUpdateProcess(string $taxonomy): void
    {
        $importMode = Settings::get_setting('terms_mode', 'first-200');
        if ($importMode == 'first-200') {
            $after_key = false;
        } elseif ($importMode == 'all') {
            $after_key = get_option('realtyna_mls_on_the_fly_taxonomy_after_key_' . $taxonomy, '');
        } else {
            Settings::update_setting('terms_mode', 'first-200');
            $after_key = false;
        }
        $term_query = $this->buildTermQuery($taxonomy, $after_key);

        $RFQueries = $this->mapping->mapTermQueryToRFQuery($term_query);
        if (empty($RFQueries)) {
            return;
        }

        $result = [];
        foreach ($RFQueries as $RFQuery) {
            Log::info('Executing query:', [$RFQuery]);
            $RFResponse = $this->RF->get($RFQuery);
            $result = array_merge($RFResponse->items, $result);
        }

        $RFTerms = RFTermEntity::createFromArray($result);
        $WPTerms = $this->mapping->mapRFTermsToWPTerms($RFTerms, $taxonomy);


        $childTerm = $this->mapping->mappingConfig->getQueryTaxonomyChild($taxonomy);
        $ChildWPTerms = [];
        if ($childTerm) {
            $currentTerms = get_terms(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => false, // Set to true to hide terms without posts
                'parent' => 0,
            ));
            foreach ($currentTerms as $currentTerm) {
                $replaces = $this->mapping->mappingConfig->getQueryTaxonomyReplaces($taxonomy);
                if ($replaces) {
                    foreach ($replaces as $replace) {
                        if ($currentTerm->name == $replace['replace']) {
                            $currentTerm->name = $replace['search'];
                            $currentTerm->slug = sanitize_title($replace['search']);
                        }
                    }
                }

                $result = [];
                $term_query = $this->buildTermQuery(taxonomy: $taxonomy, parent: $currentTerm->name);
                $RFQueries = $this->mapping->mapTermQueryToRFQuery($term_query);
                foreach ($RFQueries as $RFQuery) {
                    Log::info('Executing query:', [$RFQuery]);
                    $RFResponse = $this->RF->get($RFQuery);
                    $result = array_merge($RFResponse->items, $result);
                    $RFTerms = RFTermEntity::createFromArray($result);
                    $ChildWPTerms = array_merge(
                        $this->mapping->mapRFTermsToWPTerms($RFTerms, $taxonomy, $currentTerm),
                        $ChildWPTerms
                    );
                }
            }
        }
        $WPTerms = array_merge($WPTerms, $ChildWPTerms);
        $this->updateTermsInDatabase($WPTerms, $taxonomy);
        // Save the last update time and after_key for the taxonomy
        if (!$RFResponse->after_key) {
            update_option('realtyna_mls_on_the_fly_taxonomy_last_update_time_' . $taxonomy, time());
        }
        update_option('realtyna_mls_on_the_fly_taxonomy_after_key_' . $taxonomy, $RFResponse->after_key);
    }

    /**
     * Build the WP_Term_Query object for the given taxonomy.
     */
    function buildTermQuery(string $taxonomy, string|bool $after_key = false, ?string $parent = ''): WP_Term_Query
    {
        $args = array(
            'taxonomy' => $taxonomy,
            // Add any additional query parameters here if needed
        );

        if ($after_key !== false) {
            $args['after_key'] = $after_key;
        }

        if ($parent) {
            $args['parent_name'] = $parent;
        }

        return new WP_Term_Query($args);
    }

    /**
     * Insert or update the terms in the WordPress database.
     */
    function updateTermsInDatabase(array $WPTerms, string $taxonomy): void
    {
        // Fetch all existing terms once
        $existing_terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ));

        // Create a map of slug => term_id for quick lookup
        $existing_terms_map = [];
        foreach ($existing_terms as $existing_term) {
            $existing_terms_map[$existing_term->slug] = $existing_term->term_id;
        }
        foreach ($WPTerms as $WPTerm) {
            $count = $WPTerm->count;
            if (isset($existing_terms_map[$WPTerm->slug])) {
                // Update the term if it exists
                $term = wp_update_term($existing_terms_map[$WPTerm->slug], $taxonomy, array(
                    'count' => $WPTerm->count,
                    // Add any additional data here as needed
                ));
            } else {
                // Insert the term if it doesn't exist
                $term = wp_insert_term($WPTerm->name, $taxonomy, array(
                    'slug' => $WPTerm->slug,
                    'count' => $WPTerm->count,
                    'parent' => $WPTerm->parent,
                    // Add any additional data here as needed
                ));
            }
            if (!($term instanceof WP_Error)) {
                $this->updateTermCount($term['term_id'], $term['term_taxonomy_id'], $count);
            }
        }
    }

    /**
     * Update term count
     *
     * @param int $term_id
     * @param int $count
     *
     * @return void
     * @author Cyrus <cyrus.a@realtyna.com>
     *
     */
    public function updateTermCount(int $term_id, int $term_taxonomy_id, int $count): void
    {
        if (!$term_id) {
            return;
        }

        update_term_meta($term_id, 'realtyna_mls_on_the_fly_term_count', $count);

        global $wpdb;
        $wpdb->update($wpdb->term_taxonomy, array('count' => $count), array('term_taxonomy_id' => $term_taxonomy_id));
    }
}