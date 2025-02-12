<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\XML_Sitemaps
 */

namespace Realtyna\MlsOnTheFly\Components\CloudPost\SubComponents\Integration\Targets;

use OutOfBoundsException;
use WP_Query;
use WPSEO_Meta;
use WPSEO_Post_Type;
use WPSEO_Sitemap_Image_Parser;
use WPSEO_Sitemap_Provider;
use WPSEO_Sitemaps;
use WPSEO_Sitemaps_Router;
use Yoast\WP\SEO\Models\SEO_Links;

/**
 * Sitemap provider for the property post type.
 */
class WPSEO_Cloud_Post_Type_Sitemap_Provider implements WPSEO_Sitemap_Provider {

    protected static $image_parser;
    protected static $parsed_home_url;
    private $include_images;
    private string $last_modification_date;

    public function __construct() {
        $this->include_images = false;
    }

    protected function get_image_parser() {
        if ( ! isset( self::$image_parser ) ) {
            self::$image_parser = new WPSEO_Sitemap_Image_Parser();
        }
        return self::$image_parser;
    }

    protected function get_parsed_home_url() {
        if ( ! isset( self::$parsed_home_url ) ) {
            self::$parsed_home_url = wp_parse_url( home_url() );
        }
        return self::$parsed_home_url;
    }

    public function handles_type( $type ) {
        return $type === 'cloud-property';
    }

    public function get_index_links( $max_entries ) {
        $post_types = [ 'property' ];
        $index = [];
        $last_modified_times = [];

        foreach ( $post_types as $post_type ) {
            $total_count = $this->get_post_type_count( $post_type );
            $last_modified_times[$post_type] = $this->last_modification_date;
            if ( $total_count === 0 ) {
                continue;
            }

            $max_pages = ( $total_count > $max_entries ) ? (int) ceil( $total_count / $max_entries ) : 1;
            $all_dates = ( $max_pages > 1 ) ? $this->get_all_dates( $post_type, $max_entries ) : [];

            for ( $page_counter = 0; $page_counter < $max_pages; $page_counter++ ) {
                $current_page = ( $page_counter === 0 ) ? '' : ( $page_counter + 1 );
                $date = ( empty( $current_page ) || $current_page === $max_pages ) ? $last_modified_times[ $post_type ] : $all_dates[ $page_counter ];

                $index[] = [
                    'loc'     => WPSEO_Sitemaps_Router::get_base_url( 'cloud-' . $post_type . '-sitemap' . $current_page . '.xml' ),
                    'lastmod' => $date,
                ];
            }
        }


        return $index;
    }

    public function get_sitemap_links( $type, $max_entries, $current_page ) {
        if ( $type !== 'cloud-property' ) {
            throw new OutOfBoundsException( 'Invalid sitemap page requested' );
        }
        $type = 'property';
        $links = [];
        $steps = min( 100, $max_entries );
        $offset = ( $current_page > 1 ) ? ( ( $current_page - 1 ) * $max_entries ) : 0;
        $total = ( $offset + $max_entries );
        $post_type_entries = $this->get_post_type_count( $type );

        if ( $total > $post_type_entries ) {
            $total = $post_type_entries;
        }

        if ( $current_page === 1 ) {
            $links = array_merge( $links, $this->get_first_links( $type ) );
        }

        if ( $post_type_entries < $offset ) {
            throw new OutOfBoundsException( 'Invalid sitemap page requested' );
        }

        if ( $post_type_entries === 0 ) {
            return $links;
        }


        while ( $total > $offset ) {
            $posts = $this->get_posts( $steps, $offset );
            $offset += $steps;

            if ( empty( $posts ) ) {
                continue;
            }

            foreach ( $posts as $post ) {

                if ( WPSEO_Meta::get_value( 'meta-robots-noindex', $post->ID ) === '1' ) {
                    continue;
                }

                $url = $this->get_url( $post );

                if ( ! isset( $url['loc'] ) ) {
                    continue;
                }

                $url = apply_filters( 'wpseo_sitemap_entry', $url, 'post', $post );

                if ( ! empty( $url ) ) {
                    $links[] = $url;
                }
            }
        }
        return $links;
    }

    public function is_valid_post_type( $post_type ) {
        return $post_type === 'property' && WPSEO_Post_Type::is_post_type_accessible( $post_type ) && WPSEO_Post_Type::is_post_type_indexable( $post_type ) && ! apply_filters( 'wpseo_sitemap_exclude_post_type', false, $post_type );
    }

    protected function get_post_type_count( $post_type ) {
        $args = [
            'post_type'      => $post_type,
            'posts_per_page' => 1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        $query = new WP_Query( $args );

        if(count($query->posts) > 0 && isset($query->posts[0])){
            $lastPost = $query->posts[0];
            $this->last_modification_date = $lastPost->post_date;
        }
        return $query->found_posts;
    }


    protected function get_first_links( $post_type ) {
        $links = [];
        $archive_url = false;

        if ( $post_type === 'property' ) {
            $archive_url = get_post_type_archive_link( $post_type );
        }

        if ( $archive_url ) {
            $links[] = [
                'loc' => $archive_url,
                'mod' => WPSEO_Sitemaps::get_last_modified_gmt( $post_type ),
                'chf' => 'daily',
                'pri' => 1,
            ];
        }

        return apply_filters( 'wpseo_sitemap_post_type_first_links', $links, $post_type );
    }

    protected function get_posts($count, $offset ) {
        $args = [
            'post_type'      => 'property',
            'posts_per_page' => $count,
            'offset'         => $offset,
            'orderby'        => 'modified',
            'order'          => 'ASC',
        ];

        $query = new WP_Query( $args );
        $posts = $query->posts;
        $post_ids = wp_list_pluck( $posts, 'ID' );

        update_meta_cache( 'post', $post_ids );

        return $posts;
    }

    protected function get_url( $post ) {
        $url = [];
        $url['loc'] = apply_filters( 'wpseo_xml_sitemap_post_url', get_permalink( $post ), $post );
        $link_type  = YoastSEO()->helpers->url->get_link_type( wp_parse_url( $url['loc'] ), $this->get_parsed_home_url() );

        if ( $link_type === SEO_Links::TYPE_EXTERNAL ) {
            return false;
        }

        $modified = max( $post->post_modified_gmt, $post->post_date_gmt );

        if ( $modified !== '0000-00-00 00:00:00' ) {
            $url['mod'] = $modified;
        }

        $url['chf'] = 'daily';

        $canonical = WPSEO_Meta::get_value( 'canonical', $post->ID );

        if ( $canonical && $canonical !== $url['loc'] ) {
            return false;
        }

        $url['pri'] = 1;

        if ( $this->include_images ) {
            $this->get_image_parser()->parse_images( $post );
            $images = $this->get_image_parser()->get_images();

            if ( ! empty( $images ) ) {
                $url['images'] = $images;
            }
        }

        return $url;
    }

    protected function get_all_dates( $post_type, $max_entries ) {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT post_modified_gmt
            FROM $wpdb->posts
            WHERE post_type = %s
                AND post_status IN ( 'publish', 'inherit' )
                AND ( post_password = '' OR post_password IS NULL )
            ORDER BY post_modified_gmt ASC",
            $post_type
        );

        $dates = $wpdb->get_col( $query );

        if ( empty( $dates ) ) {
            return [];
        }

        $date_index = [];
        $total_dates = count( $dates );

        for ( $i = 1; $i <= ceil( $total_dates / $max_entries ); $i++ ) {
            $position = $i * $max_entries;
            $position = ( $position >= $total_dates ) ? ( $total_dates - 1 ) : $position;
            $date_index[] = $dates[ $position ];
        }

        return $date_index;
    }

}
