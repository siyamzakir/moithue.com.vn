<?php 
global $settings, $paged;
if ( is_front_page()  ) {
    $paged = (get_query_var('page')) ? get_query_var('page') : 1;
}

$agency_id = get_the_ID();

$tax_query = array();
$meta_query = array();

if ( isset( $_GET['tab'] ) && !empty($_GET['tab']) && $_GET['tab'] != "reviews" && $_GET['tab'] != "all") {
    $tax_query[] = array(
        'taxonomy' => 'property_status',
        'field' => 'slug',
        'terms' => $_GET['tab']
    );
}

$posts_limit = $settings['posts_limit'] ?? 12;
$pagination_type = $settings['pagination_type'] ?? '_loadmore';

$args = array(
    'post_type' => 'property',
    'posts_per_page' => intval($posts_limit),
    'paged' => $paged,
    'post_status' => 'publish',
);

$args = apply_filters( 'houzez_sold_status_filter', $args );

$agents_array = array();
$agency_properties_ids = array();
$agents_properties_ids = array();

$agency_agents_ids = Houzez_Query::loop_agency_agents_ids(get_the_ID());

$agency_properties_ids = Houzez_Query::get_property_ids_by_agency(get_the_ID());

if (!empty($agency_agents_ids)) {
    $agents_properties_ids = Houzez_Query::get_property_ids_by_agents($agency_agents_ids);
}

$properties_ids = array_merge( $agency_properties_ids, $agents_properties_ids );
$properties_ids = array_unique( $properties_ids );


if (!empty($properties_ids)) {
    $args['post__in'] = $properties_ids;
} else {
    $args['post__in'] = array(-1); // To return no results if no properties are found.
}


$tax_count = count($tax_query);
if($tax_count > 0 ) {
    $args['tax_query'] = $tax_query;
}


$args = houzez_prop_sort($args);

$the_query = new WP_Query( $args );
$agency_total_listing = $the_query->found_posts;

$item_layout = $view_class = $cols_in_row = '';
$card_deck = 'card-deck';
$listing_view = $settings['listings_layout'];

if($listing_view == 'list-view-v1') {
    $wrap_class = 'listing-v1';
    $item_layout = 'v1';
    $view_class = 'list-view';

} elseif($listing_view == 'grid-view-v1') {
    $wrap_class = 'listing-v1';
    $item_layout = 'v1';
    $view_class = 'grid-view';

} elseif($listing_view == 'list-view-v2') {
    $wrap_class = 'listing-v2';
    $item_layout = 'v2';
    $view_class = 'list-view';

} elseif($listing_view == 'grid-view-v2') {
    $wrap_class = 'listing-v2';
    $item_layout = 'v2';
    $view_class = 'grid-view';

} elseif($listing_view == 'grid-view-v3') {
    $wrap_class = 'listing-v3';
    $item_layout = 'v3';
    $view_class = 'grid-view';

} elseif($listing_view == 'grid-view-v4') {
    $wrap_class = 'listing-v4';
    $item_layout = 'v4';
    $view_class = 'grid-view';

} elseif($listing_view == 'list-view-v4') {
    $wrap_class = 'listing-v4';
    $item_layout = 'list-v4';
    $view_class = 'list-view listing-view-v4';

} elseif($listing_view == 'list-view-v5') {
    $wrap_class = 'listing-v5';
    $item_layout = 'v5';
    $view_class = 'list-view';

} elseif($listing_view == 'grid-view-v5') {
    $wrap_class = 'listing-v5';
    $item_layout = 'v5';
    $view_class = 'grid-view';

} elseif($listing_view == 'grid-view-v6') {
    $wrap_class = 'listing-v6';
    $item_layout = 'v6';
    $view_class = 'grid-view';

} elseif($listing_view == 'grid-view-v7') {
    $wrap_class = 'listing-v7';
    $item_layout = 'v7';
    $view_class = 'grid-view';

} elseif($listing_view == 'list-view-v7') {
    $wrap_class = 'listing-v7';
    $item_layout = 'list-v7';
    $view_class = 'list-view';
    $card_deck = '';
} else {
    $wrap_class = 'listing-v1';
    $item_layout = 'v1';
    $view_class = 'grid-view';
}

$active_reviews_content = $active_reviews_tab = '';
$active_listings_tab = 'active';
$active_listings_content = 'show active';
$active_agents_tab = '';

if(isset($_GET['tab']) || $paged > 0) {

    if(isset($_GET['tab']) && $_GET['tab'] == 'reviews') {
        $active_reviews_tab = 'active';
        $active_reviews_content = 'show active';
        $active_listings_tab = '';
        $active_listings_content = '';
    }
    ?>
    <script>
        jQuery(document).ready(function ($) {
            $('html, body').animate({
                scrollTop: $(".agent-nav-wrap").offset().top
            }, 'slow');
        });
    </script>
    <?php
}

$agents_query = Houzez_Query::loop_agency_agents(get_the_ID());
?>
<div id="review-scroll" class="agent-nav-wrap">
    <ul class="nav nav-pills nav-justified">
        
        <li class="nav-item">
            <a class="nav-link <?php echo esc_attr($active_listings_tab); ?>" href="#tab-properties" data-toggle="pill" role="tab">
                <?php esc_html_e('Listings', 'houzez'); ?> (<?php echo esc_attr($agency_total_listing); ?>)
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo esc_attr($active_agents_tab); ?>" href="#tab-agents" data-toggle="pill" role="tab">
                <?php echo esc_html__('Agents', 'houzez'); ?> (<?php echo esc_attr($agents_query->found_posts); ?>)
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link hz-review-tab <?php echo esc_attr($active_reviews_tab); ?>" href="#tab-reviews" data-toggle="pill" role="tab">
                <?php esc_html_e('Reviews', 'houzez'); ?> (<?php echo houzez_reviews_count('review_agency_id'); ?>)
            </a>
        </li>
    </ul>
</div><!-- agent-nav-wrap -->

<div class="tab-content" id="tab-content">
    
    <div class="tab-pane fade <?php echo esc_attr($active_listings_content); ?>" id="tab-properties">
        <div class="listing-tools-wrap">
            <div class="d-flex align-items-center">
                <div id="houzez-listings-tabs-wrap" class="listing-tabs flex-grow-1">
                    <?php 
                    if( $settings['listing_tabs'] == 'yes' ) {
                        htf_get_template_part('elementor/template-part/listing-tabs'); 
                    }?> 
                </div>
                <?php get_template_part('template-parts/listing/listing-sort-by'); ?>  
            </div><!-- d-flex -->
        </div><!-- listing-tools-wrap -->

        <section class="listing-wrap <?php echo esc_attr($wrap_class); ?>">
            <div class="listing-view <?php echo esc_attr($view_class).' '.esc_attr($card_deck).' '.esc_attr($settings['module_type']); ?>">
                <?php
                if ( $the_query->have_posts() ) :
                    while ( $the_query->have_posts() ) : $the_query->the_post();

                        $agent_listing_ids[] = get_the_ID(); 
                        get_template_part('template-parts/listing/item', $item_layout);

                    endwhile;
                    wp_reset_postdata();
                else:
                    get_template_part('template-parts/listing/item', 'none');
                endif;
                ?> 
            </div><!-- listing-view -->

            <?php houzez_pagination( $the_query->max_num_pages, $agency_total_listing, $posts_limit, $pagination_type ); ?>
        </section>
    </div><!-- tab-pane -->

    <div class="tab-pane fade" id="tab-agents">
        <div class="agents-list-view">
            <?php
            if ( $agents_query->have_posts() ) :
                while ( $agents_query->have_posts() ) : $agents_query->the_post();

                    get_template_part('template-parts/realtors/agent/list');

                endwhile;
                wp_reset_postdata();
            else:
                get_template_part('template-parts/realtors/agent/none');
            endif;
            ?> 
        </div><!-- listing-view -->
    </div><!-- tab-pane -->

    <div class="tab-pane fade <?php echo esc_attr($active_reviews_content); ?>" id="tab-reviews">
        <?php htf_get_template_part('elementor/template-part/single-agency/agency-reviews'); ?>
    </div><!-- tab-pane -->
</div><!-- tab-content -->