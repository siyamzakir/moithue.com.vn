<?php
if( !function_exists('houzez_property_by_ids') ) {
    function houzez_property_by_ids($atts, $content = null)
    {

        extract(shortcode_atts(array(
            'prop_grid_style' => '',
            'columns' => '',
            'property_ids' => ''
        ), $atts));

        ob_start();

        $cards_class = 'property-by-ids-module-1';
        $columns_class = 'property-by-ids-module-3-cols';
        $cols_class = "grid-view-3-cols";
        $cards_version = 'v1';
        $listing_view = 'grid-view card-deck';

        if( $prop_grid_style == "v_1" ) {
            $cards_version = 'v1';

        } elseif( $prop_grid_style == "v_2" ) {
            $cards_class = 'property-by-ids-module-2';
            $cards_version = 'v2';

        } elseif( $prop_grid_style == "v_3" ) {
            $cards_class = 'property-by-ids-module-3';
            $cards_version = 'v3';
            
        } elseif( $prop_grid_style == "v_4" ) {
            $cards_version = 'v4';
            
        } elseif( $prop_grid_style == "v_5" ) {
            $cards_version = 'v5';
            
        } elseif( $prop_grid_style == "v_6" ) {
            $cards_version = 'v6';

        } elseif( $prop_grid_style == "v_7" ) {
            $cards_version = 'v7';
            
        } elseif( $prop_grid_style == "v_8" ) {
            $cards_version = 'list-v4';
            
        } 

        if($columns == '2cols') {
            $columns_class = 'property-by-ids-module-2-cols';
            $cols_class = "grid-view-2-cols";
        }

        if($columns == '3cols' && $prop_grid_style == "v_4") {
            $columns_class = 'property-by-ids-module-2-cols';
        }

        if( $cards_version == 'list-v4' ) {
            $columns_class = '';
            $cols_class = '';
            $cards_class = '';
            $listing_view = 'list-view';
        }

        $ids_array = explode(',', $property_ids);

        $args = array(
            'post_type' => 'property',
            'post__in' => $ids_array,
            'posts_per_page' => -1,
            'post_status' => array('publish', 'houzez_sold')
        );

        //do the query
        $the_query = New WP_Query($args);
        ?>

        <div class="property-by-ids-module <?php echo esc_attr($columns_class); ?> <?php echo esc_attr($cards_class); ?>">
            <div class="listing-view <?php echo esc_attr($listing_view); ?> <?php echo esc_attr($cols_class); ?>">
                <?php
                if ( $the_query->have_posts() ) :
                    while ( $the_query->have_posts() ) : $the_query->the_post();

                        get_template_part('template-parts/listing/item', $cards_version);

                    endwhile;
                else:
                    get_template_part('template-parts/listing/item', 'none');
                endif;
                wp_reset_postdata();
                ?>
            </div><!-- listing-view -->
        </div><!-- property-by-is-module -->

        <?php
        $result = ob_get_contents();
        ob_end_clean();
        return $result;

    }

    add_shortcode('houzez-prop-by-ids', 'houzez_property_by_ids');
}
?>
