<?php
/**
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 06/10/15
 * Time: 4:12 PM
 */

$user_id = get_current_user_id();
$is_admin = houzez_is_admin();
$is_logged_in = is_user_logged_in();

if(isset($_POST['propperty_image_ids']) && is_array($_POST['propperty_image_ids'])) {
    $image_id = $_POST['propperty_image_ids'][0] ?? 0;

    if($image_id) {
        $image_post = DB::findByColumns(DB::POSTS, ['ID' => $image_id], ['ID', 'post_parent']);
        $update = DB::updateByColumns(DB::POSTS, ['ID' => $image_post['post_parent']], ['poster_id' => $user_id], false);
    }
}

$user_show_roles = houzez_option('user_show_roles');
$show_hide_roles = houzez_option('show_hide_roles');
$enable_paid_submission = houzez_option('enable_paid_submission');
$select_packages_link = houzez_get_template_link('template/template-packages.php'); 

$agent_agency_id = houzez_get_agent_agency_id($user_id);
if($agent_agency_id) {
    $user_id = $agent_agency_id;
}

$remaining_listings = houzez_get_remaining_listings($user_id);
$show_submit_btn = houzez_option('submit_form_type');

$cancel_link = houzez_dashboard_listings();
if(!$is_logged_in) {
  $cancel_link = home_url('/');  
}

$allowed_html = [
    'i' => [
        'class' => []
    ],
    'strong' => [],
    'a' => [
        'href' => [],
        'title' => [], 
        'target' => []
    ]
];

if(is_page_template( 'template/user_dashboard_submit.php')) {
    if ($is_logged_in && !$is_admin && $enable_paid_submission === 'membership') {
        // Check if user has reached listing limit
        $has_reached_limit = $remaining_listings !== -1 && $remaining_listings < 1;
        
        if ($has_reached_limit) {
            $message = '';
            $button_text = '';
            
            if (!houzez_user_has_membership($user_id)) {
                $message = esc_html__("You don't have any package! You need to buy your package.", 'houzez');
                $button_text = esc_html__('Get Package', 'houzez');
            } else {
                $message = esc_html__("Your current package doesn't let you publish more properties! You need to upgrade your membership.", 'houzez');
                $button_text = esc_html__('Upgrade Package', 'houzez');
            }
            
            printf(
                '<div class="dashboard-content-block-wrap">
                    <div class="dashboard-content-block">
                        <p>%s</p>
                        <a class="btn btn-primary" href="%s">%s</a>
                    </div>
                </div>',
                $message,
                esc_url($select_packages_link),
                $button_text
            );
        }
    } else { 
        // Define form sections and their templates
        $form_sections = [
            'description-price' => 'description-and-price',
            'media' => 'media',
            'details' => 'details',
            'energy_class' => 'energy-class',
            'features' => 'features',
            'location' => 'location',
            'virtual_tour' => '360-virtual-tour',
            'floorplans' => ['floor', 'plans'],
            'multi-units' => 'sub-properties',
            'agent_info' => [
                'template' => 'contact-information',
                'condition' => 'houzez_show_agent_box'
            ],
            'private_note' => 'private-note',
            'attachments' => 'attachments'
        ];
        ?>
    
        <form autocomplete="off" 
              id="submit_property_form" 
              name="new_post" 
              method="post" 
              action="#" 
              enctype="multipart/form-data"
              class="add-frontend-property" 
              novalidate>
    
            <!-- Validation Error Messages -->
            <div class="validate-errors alert alert-danger houzez-hidden" role="alert">
                <?php echo wp_kses(__('<strong>Error!</strong> Please fill out the following required fields.', 'houzez'), $allowed_html); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
    
            <div class="validate-errors-gal alert alert-danger houzez-hidden" role="alert">
                <?php echo wp_kses(__('<strong>Error!</strong> Upload at least one image.', 'houzez'), $allowed_html); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
    
            <!-- Mobile Edit Menu -->
            <div class="dashboard-mobile-edit-menu-wrap">
                <div class="form-group">
                    <?php get_template_part('template-parts/dashboard/submit/partials/author-mobile'); ?>
                </div>
            </div>
    
            <?php
            // Load form sections based on enabled layout
            $layout = houzez_option('property_form_sections');
            $enabled_sections = isset($layout['enabled']) ? $layout['enabled'] : [];
    
            if ($enabled_sections) {
                foreach ($enabled_sections as $key => $value) {
                    if (isset($form_sections[$key])) {
                        if (is_array($form_sections[$key])) {
                            if (isset($form_sections[$key]['condition'])) {
                                // Section with condition
                                if (function_exists($form_sections[$key]['condition']) && call_user_func($form_sections[$key]['condition'])) {
                                    get_template_part('template-parts/dashboard/submit/' . $form_sections[$key]['template']);
                                }
                            } else {
                                // Section with multiple parts
                                get_template_part('template-parts/dashboard/submit/' . $form_sections[$key][0], $form_sections[$key][1]);
                            }
                        } else {
                            // Simple section
                            get_template_part('template-parts/dashboard/submit/' . $form_sections[$key]);
                        }
                    }
                }
            }
    
            // Additional conditional sections
            if (houzez_is_admin() || houzez_is_editor()) {
                get_template_part('template-parts/dashboard/submit/settings');
            }
    
            if (houzez_option('add-prop-gdpr-enabled')) {
                get_template_part('template-parts/dashboard/submit/gdpr');
            }
    
            if (!is_user_logged_in()) {
                get_template_part('template-parts/dashboard/submit/account');
            }
            ?>
    
            <!-- Hidden Fields -->
            <?php wp_nonce_field('submit_property', 'property_nonce'); ?>
            <input type="hidden" name="action" value="add_property"/>
            <input type="hidden" name="property_author" value="<?php echo intval($user_id); ?>"/>
            
            <?php if (!houzez_is_admin()): ?>
                <input type="hidden" name="prop_featured" value="0"/>
            <?php endif; ?>
    
            <input type="hidden" name="prop_payment" value="not_paid"/>
            
            <?php if (!is_user_logged_in()): ?>
                <input type="hidden" name="user_submit_has_no_membership" value="yes"/>
            <?php endif; ?>
    
            <!-- Form Navigation -->
            <div class="d-flex justify-content-between add-new-listing-bottom-nav-wrap">
                <a href="<?php echo esc_url($cancel_link); ?>" class="btn-cancel btn btn-primary-outlined">
                    <?php echo houzez_option('fal_cancel', esc_html__('Cancel', 'houzez')); ?>
                </a>
    
                <?php if ($show_submit_btn == 'one_step'): ?>
                    <button id="add_new_property" type="submit" class="btn houzez-submit-js btn-primary">
                        <?php get_template_part('template-parts/loader'); ?>
                        <?php echo houzez_option('fal_submit_property', esc_html__('Submit Property', 'houzez')); ?>
                    </button>
                <?php else: ?>
                    <button class="btn-back houzez-hidden btn btn-primary-outlined">
                        <i class="houzez-icon icon-arrow-left-1 mr-2"></i> 
                        <?php houzez_option('fal_back', esc_html_e('Back', 'houzez')); ?>
                    </button>
    
                    <button class="btn-next btn btn-primary">
                        <?php echo houzez_option('fal_next', esc_html__('Next', 'houzez')); ?> 
                        <i class="houzez-icon icon-arrow-right-1 ml-2"></i>
                    </button>
    
                    <div class="btn-step-submit" style="display: none;">
                        <button id="add_new_property" type="submit" class="btn houzez-submit-js btn-primary">
                            <?php get_template_part('template-parts/loader'); ?>
                            <?php echo houzez_option('fal_submit_property', esc_html__('Submit Property', 'houzez')); ?>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    <?php
    }
}
?>