<?php

namespace Realtyna\MlsOnTheFly\Database;

use Realtyna\Core\Abstracts\Database\MigrationAbstract;

class UpdateRFMappingsTableAddUniqueIndexes extends MigrationAbstract
{
    public function up(): void
    {
        global $wpdb;

        if (is_multisite()) {
            $sites = get_sites(['fields' => 'ids']);
            foreach ($sites as $site_id) {
                switch_to_blog($site_id);
                $this->updateIndexes($wpdb);
                restore_current_blog();
            }
        } else {
            $this->updateIndexes($wpdb);
        }
    }

    public function down(): void
    {
        global $wpdb;

        if (is_multisite()) {
            $sites = get_sites(['fields' => 'ids']);
            foreach ($sites as $site_id) {
                switch_to_blog($site_id);
                $this->revertIndexes($wpdb);
                restore_current_blog();
            }
        } else {
            $this->revertIndexes($wpdb);
        }
    }

    private function updateIndexes($wpdb): void
    {
        $table_name = "{$wpdb->prefix}realtyna_rf_mappings";

        // Drop existing unique index on 'listing_key' and 'post_id' if it exists
        $existingMappingIndex = $wpdb->get_var(
            $wpdb->prepare(
                "SHOW INDEX FROM $table_name WHERE Key_name = %s",
                'unique_mapping'
            )
        );

        if ($existingMappingIndex) {
            $this->runQuery("ALTER TABLE $table_name DROP INDEX unique_mapping");
        }

        // Drop the unique index on 'listing_key' if it exists
        $existingListingKeyIndex = $wpdb->get_var(
            $wpdb->prepare(
                "SHOW INDEX FROM $table_name WHERE Key_name = %s",
                'listing_key'
            )
        );

        if ($existingListingKeyIndex) {
            $this->runQuery("ALTER TABLE $table_name DROP INDEX listing_key");
        }

        // Add unique index on 'post_id' if it doesn't exist
        $postIdIndexExists = $wpdb->get_var(
            $wpdb->prepare(
                "SHOW INDEX FROM $table_name WHERE Key_name = %s",
                'unique_post_id'
            )
        );

        if (!$postIdIndexExists) {
            $this->runQuery("ALTER TABLE $table_name ADD UNIQUE KEY unique_post_id (post_id)");
        }

        // Add unique index on 'listing_key' if it doesn't exist
        $listingKeyIndexExists = $wpdb->get_var(
            $wpdb->prepare(
                "SHOW INDEX FROM $table_name WHERE Key_name = %s",
                'unique_listing_key'
            )
        );

        if (!$listingKeyIndexExists) {
            $this->runQuery("ALTER TABLE $table_name ADD UNIQUE KEY unique_listing_key (listing_key)");
        }
    }

    private function revertIndexes($wpdb): void
    {
        $table_name = "{$wpdb->prefix}realtyna_rf_mappings";

        // Drop the unique indexes
        $this->runQuery(
            "ALTER TABLE $table_name 
                DROP INDEX IF EXISTS unique_post_id, 
                DROP INDEX IF EXISTS unique_listing_key"
        );

        // Re-add the original unique index on both 'listing_key' and 'post_id'
        $this->runQuery(
            "ALTER TABLE $table_name ADD UNIQUE KEY unique_mapping (listing_key, post_id)"
        );
    }
}
