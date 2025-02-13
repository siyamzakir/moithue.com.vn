<?php 
global $post, $hide_fields, $top_area, $property_layout, $map_street_view, $loggedin_to_view;

$post_id = get_the_ID();

$single_top_area = get_post_meta($post_id, 'fave_single_top_area', true);
$single_content_area = get_post_meta($post_id, 'fave_single_content_area', true);
$map_street_view = get_post_meta($post_id, 'fave_property_map_street_view', true);
$loggedin_to_view = get_post_meta($post_id, 'fave_loggedintoview', true);
$property_live_status = get_post_status();
$hide_fields = houzez_option('hide_detail_prop_fields');
houzez_count_property_views($post->ID);

$enable_disclaimer = houzez_option('enable_disclaimer', 1);
$global_disclaimer = houzez_option('property_disclaimer');
$property_disclaimer = get_post_meta($post_id, 'fave_property_disclaimer', true);

// Override global disclaimer with post-specific one
if (!empty($property_disclaimer)) {
    $global_disclaimer = $property_disclaimer;
}

// Redirect if property status is on hold
if (($property_live_status == 'on_hold') && ($post->post_author != get_current_user_id())) {
    wp_redirect(home_url());
}

$is_sticky = '';
$sticky_sidebar = houzez_option('sticky_sidebar');
$is_full_width = houzez_option('is_full_width');
$top_area = houzez_option('prop-top-area');
$property_layout = houzez_option('prop-content-layout');

// Handle full-width layout from query string
if (isset($_GET['is_full_width'])) {
    $is_full_width = 1;
}

// Set content classes based on full width
$content_classes = ($is_full_width == 1) ? 'col-lg-12 col-md-12 bt-full-width-content-wrap' : 'col-lg-8 col-md-12 bt-content-wrap';

// Override top area and property layout with post-specific values or query string
$top_area = !empty($single_top_area) && $single_top_area != 'global' ? $single_top_area : $top_area;
$property_layout = !empty($single_content_area) && $single_content_area != 'global' ? $single_content_area : $property_layout;

// Handle layout class based on selected layout type
$layout_class = '';
if ($property_layout == 'minimal') {
    $layout_class = "content-wrap-style-minimal";
} elseif ($property_layout == 'boxed') {
    $layout_class = "content-wrap-style-boxed";
}

get_header();

// Check if Elementor is used
if (!function_exists('houzez_check_is_elementor')) {
    function elementor_theme_do_location($X) {}
}

if (houzez_check_is_elementor() && (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single'))) {
    while (have_posts()) : the_post();
        the_content();
    endwhile;
} else {
    if (!function_exists('elementor_theme_do_location') || !elementor_theme_do_location('single')) {
        if (function_exists('fts_single_listing_enabled') && fts_single_listing_enabled()) {
            do_action('houzez_single_listing');
        } else {
            if (have_posts()) : while (have_posts()) : the_post(); ?>
                <section class="content-wrap property-wrap property-detail-<?php echo esc_attr($top_area); ?> <?php echo esc_attr($layout_class); ?>">

                    <?php get_template_part('property-details/navigation'); ?>

                    <?php 
                    // Top area and title sections
                    if ($top_area != 'v5' && $top_area != 'v2') {
                        get_template_part('property-details/property-title'); 
                    }

                    // Login required or expired property
                    if ($loggedin_to_view == 1 && !is_user_logged_in()) {
                        get_template_part('property-details/partials/login_required');
                    } elseif (get_post_status($post->ID) == 'expired') {
                        get_template_part('property-details/partials/expired');
                    } else {
                        // Display content based on top area
                        switch ($top_area) {
                            case 'v1':
                                get_template_part('property-details/top-area-v1');
                                break;
                            case 'v2':
                                get_template_part('property-details/top-area-v2');
                                break;
                            case 'v3':
                            case 'v4':
                                if ($property_layout == 'v2') {
                                    echo '<div class="container">';
                                    get_template_part('property-details/top-area-v3-4');
                                    echo '</div>';
                                }
                                break;
                            case 'v5':
                                get_template_part('property-details/top-area-v5');
                                break;
                            case 'v6':
                                get_template_part('property-details/top-area-v6');
                                break;
                            case 'v7':
                                get_template_part('property-details/top-area-v7');
                                break;
                        }

                        if ($property_layout == 'v2') { ?>
                            <div class="property-view full-width-property-view">
                                <?php get_template_part('property-details/mobile-view'); ?>
                                <?php get_template_part('property-details/single-property-luxury-homes'); ?>
                            </div>

                            <?php if (!empty($global_disclaimer) && $enable_disclaimer) { ?>
                                <div class="property-disclaimer">
                                    <?php echo $global_disclaimer; ?>
                                </div>
                            <?php }
                        } else { ?>
                            <div class="container">
                                <?php 
                                if ($top_area == 'v4') {
                                    get_template_part('property-details/top-area-v3-4');
                                }
                                ?>
                                <div class="row">
                                    <div class="<?php echo esc_attr($content_classes); ?>">
                                        <?php
                                        if ($top_area == 'v3') {
                                            get_template_part('property-details/top-area-v3-4');
                                        }
                                        ?>
                                        <div class="property-view">
                                            <?php get_template_part('property-details/mobile-view'); ?>

                                            <?php
                                            if ($property_layout == 'tabs') {
                                                get_template_part('property-details/single-property', 'tabs');
                                            } elseif ($property_layout == 'tabs-vertical') {
                                                get_template_part('property-details/single-property', 'tabs-vertical');
                                            } else {
                                                get_template_part('property-details/single-property', 'simple');
                                            }

                                            if (houzez_option('enable_next_prev_prop')) {
                                                get_template_part('property-details/next-prev');
                                            }
                                            ?>
                                        </div><!-- property-view -->
                                    </div><!-- bt-content-wrap -->

                                    <?php if ($is_full_width != 1) { ?>
                                    <div class="col-lg-4 col-md-12 bt-sidebar-wrap <?php echo esc_attr($is_sticky); ?>">
                                        <?php get_sidebar('property'); ?>
                                    </div><!-- bt-sidebar-wrap -->
                                    <?php } ?>
                                </div><!-- row -->

                                <?php if (!empty($global_disclaimer) && $enable_disclaimer) { ?>
                                    <div class="property-disclaimer">
                                        <?php echo $global_disclaimer; ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <!-- container -->
                        <?php }
                    } // end logged_in_to_view

                    ?>
                </section><!-- listing-wrap -->
            <?php endwhile; endif; 
        } // End fts_single_listing_enabled else condition
    } // End elementor location check
} ?> 
<!-- end houzez_check_is_elementor -->

<!-- 
    * AppsZone Customized
    * Tue Feb 11 2025 16:20:52 GMT+0600 (Bangladesh Standard Time)
    * Powered by AppsZone
-->

<?php
$post_author_id = (int) get_post_field('post_author', $post_id);
$current_user_id = get_current_user_id();

// get the role current user's assigned role
$role = get_user_meta($current_user_id, 'wp_capabilities', true);
$is_admin = isset($role['administrator']) ? $role['administrator'] : false;

// get listing edit url
function listing_edit_url($listing_id) {
    $home = WP_HOME;
    return "{$home}/create-listing?edit_property={$listing_id}";
}

if(
    DB::hasAssignedEditor($current_user_id, $post_id) ||
    ($current_user_id && $post_author_id && ($post_author_id === $current_user_id)) || 
    $is_admin
):?>
    <div class="live-edit-btn">
        <a href="<?= listing_edit_url($post_id) ?>" target="_blank" class="live-button">
            <div class="gear-container">
                <svg class="gear-icon gear-center" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/>
                </svg>
            </div>
            <span class="live-text">Live</span>
        </a>
    </div>
<?php endif; ?>

<!-- End AppsZone Customized -->

<?php get_footer(); ?>