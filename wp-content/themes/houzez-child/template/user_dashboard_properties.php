<?php
/**
 * Template Name: User Dashboard Properties
 * Created by PhpStorm.
 * User: waqasriaz
 * Date: 15/10/15
 * Time: 3:33 PM
 * 
 * Second time edited by AppsZone
 * Created by Cursor AI & Visual Studio Code
 * User: syedamirali
 * Date: 12/02/2025
 * Time: 2:25 PM
 */

// show errors in display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in and has the necessary role
if (!is_user_logged_in() || !houzez_check_role()) {
    wp_redirect(home_url());
}

global $houzez_local, $prop_featured, $current_user, $post;

$current_user = wp_get_current_user();
$user_id = intval(get_current_user_id());
$user_login     = $current_user->user_login;
$paid_submission_type = esc_html ( houzez_option('enable_paid_submission','') );
$packages_page_link = houzez_get_template_link('template/template-packages.php');
$dashboard_add_listing = houzez_get_template_link_2('template/user_dashboard_submit.php');

$dashboard_listings = houzez_get_template_link_2('template/user_dashboard_properties.php');
$all = add_query_arg( 'prop_status', 'all', $dashboard_listings );
$mine_link = add_query_arg( 'prop_status', 'mine', $dashboard_listings );

/**
 * START: Assign Editors to a Property
 * Edited By AppsZone
 * @since 1.0.0
 * @updated 16/02/2025
 */

$users = get_users();
$messages = ['type' => '', 'message' => ''];

// Handle the deny edit action
$action = isset($_GET['action']) ? $_GET['action'] : null;

// Handle the deny edit action
if($action == 'deny_edit') {
    $get_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
    $get_post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : null;

    if($get_user_id && $get_post_id && $get_user_id === $user_id) {
        $updated = DB::removeEditorFromPost($get_user_id, $get_post_id);
        if($updated) {
            $messages['type'] = 'success';
            $messages['message'] = 'Editor removed successfully.';
        } else {
            $messages['type'] = 'danger';
            $messages['message'] = 'Failed to remove editor.';
        }
    } else {
        $messages['type'] = 'warning';
        $messages['message'] = 'Invalid request or missing parameters or you are not allowed to remove this editor.';
    }

    // Logger::info("user_dashboard_properties.php", compact('get_user_id', 'get_post_id', 'user_id', 'updated', 'action'));
    echo Section::echoDefaultUrl();
}

// Handle the assign editors action
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : null;
    $author_id = isset($_POST['author_id']) ? intval($_POST['author_id']) : null;
     
    $editor_ids = isset($_POST['editor_ids'])
        ? (
            is_array($_POST['editor_ids'])
                ? json_encode(array_values(array_map('intval', $_POST['editor_ids'])))
                : $_POST['editor_ids']
        )
        : '[]';

    $new_data = [];
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : get_current_user_id();

    try {
        if ($user_id && $post_id && $editor_ids) {
            $check = DB::findByColumns(DB::LISTING_EDITORS, ['post_id' => $post_id]);

            $new_data['post_id'] = $post_id;
            $new_data['author_id'] = $author_id;
            $new_data['user_id'] = $user_id;
            $new_data['editor_ids'] = $editor_ids;

            if ($check) {
                $new_data['update'] = DB::updateByColumns(DB::LISTING_EDITORS, [
                    'post_id' => $post_id,
                ], [
                    'user_id' => $user_id,  
                    'author_id' => $author_id,
                    'editor_ids' => $editor_ids
                ]);

                if($new_data['update']) {
                    $messages['type'] = 'success';
                    $messages['message'] = 'Successfully updated the assigned editors for the property.';
                } else {
                    $messages['type'] = 'danger';
                    $messages['message'] = 'Failed to update the assigned editors for the property.';
                }
            } else {
                $new_data['create'] = DB::create(DB::LISTING_EDITORS, [
                    'post_id' => $post_id,
                    'author_id' => $author_id,
                    'user_id' => $user_id,
                    'editor_ids' => $editor_ids
                ]);

                if($new_data['create']) {
                    $messages['type'] = 'success';
                    $messages['message'] = 'New editors assigned successfully to the property.';
                } else {
                    $messages['type'] = 'danger';
                    $messages['message'] = 'Failed to assign editors to the property.';
                }
            }
        } else {
            $messages['type'] = 'warning';
            $messages['message'] = 'Invalid request or missing parameters.';
        }
    } catch (Exception $e) {
        $messages['type'] = 'danger';
        $messages['message'] = 'Failed to assign editors to the property.';
        Logger::error("Error: " . $e->getMessage());
    }

    echo Section::echoDefaultUrl();
}

/* 
 * END: Assign Editors to a Property
 */

get_header();

// Get 'prop_status' parameter from URL and set 'qry_status' accordingly
$prop_status = isset($_GET['prop_status']) ? $_GET['prop_status'] : null;
switch ($prop_status) {
    case 'approved':
        $qry_status = 'publish';
        break;
    case 'pending':
    case 'expired':
    case 'disapproved':
    case 'draft':
    case 'on_hold':
        $qry_status = $prop_status;
        break;
    default:
        $qry_status = 'any';
}

// Get 'sortby' parameter if set
$sortby = isset($_GET['sortby']) ? $_GET['sortby'] : '';

// Default number of properties and page number
$no_of_prop = 12;
$paged = get_query_var('paged') ?: get_query_var('page') ?: 1;

// Define the initial args for the WP query
$args = [
    'post_type'      => 'property',
    'paged'          => $paged,
    'posts_per_page' => $no_of_prop,
    'post_status'    => [$qry_status],
    'suppress_filters' => false, 
    // 'post__in' => [361]
];

/* Start Edited By AppsZone */
$args = houzez_prop_sort($args);
$is_houzez_manager = houzez_is_admin() || houzez_is_editor();

/* End Edited By AppsZone */

if($is_houzez_manager) {
    if(isset( $_GET['user'] ) && !empty($_GET['user'])) {
        $args['author'] = intval($_GET['user']);

    } else if(isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'mine' ) {
        $args['author'] = $user_id;
    }
} else if(houzez_is_agency()) {
    $agents = houzez_get_agency_agents($user_id);
    
    if(isset( $_GET['user'] ) && !empty($_GET['user'])) {
        $requested_user = intval($_GET['user']);
        // Only set author if requested user is current user or one of their agents
        if($requested_user === $user_id || in_array($requested_user, $agents)) {
            $args['author'] = $requested_user;
        } else {
            // If requested user is not authorized, show no properties
            $args['author'] = -1; // This will return no results
        }
    } else if(isset( $_GET['prop_status'] ) && $_GET['prop_status'] == 'mine') {
        $args['author'] = $user_id;
    } else if($agents && !empty($agents)) {
        if (!in_array($user_id, $agents)) {
            $agents[] = $user_id;
        }
        $args['author__in'] = $agents;
    } else {
        $args['author'] = $user_id;
    }
} else {
    $args['author'] = $user_id;
}


// Add keyword search to args if set
if (!empty($_GET['keyword'])) {
    $args['s'] = trim($_GET['keyword']);
}

// Add property ID to meta query if set
if (!empty($_GET['property_id'])) {
    
    $meta_query[] = array(
        'key' => 'fave_property_id',
        'value' => $_GET['property_id'],
        'type' => 'CHAR',
        'compare' => '=',
    );
    
    $meta_count = count($meta_query);

    if( $meta_count > 1 ) {
        $meta_query['relation'] = 'AND';
    }

    if ($meta_count > 0) {
        $args['meta_query'] = $meta_query;
    }
}

/* Start Edited By AppsZone */
$ids = DB::getPostIdsByEditorId($user_id);
// Logger::info("ids", compact('ids', 'user_id'));

if(!$is_houzez_manager && !empty($ids)) {
    $default_author = DB::MAIN_ADMINISTRATOR_ID;
    
    if($default_author && is_numeric($default_author)) {
        $args['author'] = $default_author;
    } else {
        unset($args['author']);
    }

    $args['post__in'] = $ids;
}

// Logger::info("user_dashboard_properties.php", compact('args'));
/* End Edited By AppsZone */
?>

<header class="header-main-wrap dashboard-header-main-wrap">
    <div class="dashboard-header-wrap">

        <!-- Handle Messages with cancel button -->
        <?php if($messages['type'] && $messages['message']) { ?>
            <div class="alert alert-<?php echo $messages['type']; ?>">
                <?php echo $messages['message']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-top: -10px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>
        <div class="d-flex align-items-center">
            <div class="dashboard-header-left flex-grow-1">
                <h1><?php echo houzez_option('dsh_props', 'Properties'); ?></h1>         
            </div><!-- dashboard-header-left -->

            <?php if(!empty($dashboard_add_listing)) { ?>
            <div class="dashboard-header-right">
                <a class="btn btn-primary" href="<?php echo esc_url($dashboard_add_listing); ?>"><?php echo houzez_option('dsh_create_listing', 'Create a Listing'); ?></a>
            </div><!-- dashboard-header-right -->
            <?php } ?>
        </div><!-- d-flex -->
    </div><!-- dashboard-header-wrap -->
</header><!-- .header-main-wrap -->
<section class="dashboard-content-wrap">
    <div class="dashboard-content-inner-wrap">
        <div class="dashboard-content-block-wrap">

            <div class="dashboard-property-search-wrap">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <div class="dashboard-property-search">
                            <?php get_template_part('template-parts/dashboard/property/search'); ?>
                        </div>
                    </div>
                    <div class="dashboard-property-sort-by">
                        <?php get_template_part('template-parts/listing/listing-sort-by'); ?>  
                    </div>
                </div>
            </div><!-- dashboard-property-search -->

            <?php
            $prop_qry = new WP_Query($args); 
            if( $prop_qry->have_posts() ): ?>
                <div id="dash-prop-msg"></div>
                <table class="dashboard-table dashboard-table-properties table-lined table-hover responsive-table">
                <thead>
                    <tr>
                        <th><?php echo esc_html__('Thumbnail', 'houzez'); ?></th>
                        <th><?php echo esc_html__('Title', 'houzez'); ?></th>
                        <th></th>
                        <th><?php echo esc_html__('Type', 'houzez'); ?></th>
                        <th><?php echo esc_html__('Status', 'houzez'); ?></th>
                        <th><?php echo esc_html__('Price', 'houzez'); ?></th>
                        <th><?php echo esc_html__('Featured', 'houzez'); ?></th>
                        <th><?php echo esc_html__('Posted', 'houzez'); ?></th>
                        <th class="action-col"><?php echo esc_html__('Actions', 'houzez'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        while ($prop_qry->have_posts()): $prop_qry->the_post();
                            get_template_part('template-parts/dashboard/property/property-item');
                        endwhile; 
                    ?>

                </tbody>
                </table><!-- dashboard-table -->

                <?php houzez_pagination( $prop_qry->max_num_pages ); ?> 

            <?php    
            else: 

                if(isset($_GET['keyword'])) {

                    echo '<div class="dashboard-content-block">
                        '.esc_html__("No results found", 'houzez').'
                    </div>';

                } else {
                    echo '<div class="dashboard-content-block">
                        '.esc_html__("You don't have any property listed.", 'houzez').' <a href="'.esc_url($dashboard_add_listing).'"><strong>'.esc_html__('Create a listing', 'houzez').'</strong></a>
                    </div>';
                }
                

            endif;
            ?>
        
        </div><!-- dashboard-content-block-wrap -->
    </div><!-- dashboard-content-inner-wrap -->
</section><!-- dashboard-content-wrap -->
<section class="dashboard-side-wrap">
    <?php get_template_part('template-parts/dashboard/side-wrap'); ?>
</section>


<!--
    * Customized By AppsZone    
    * Script to assign editors to a property
    * Date: 12/02/2025
    * Time: 09:08 PM
    * 
-->
    
<!-- Popup form for assign editors to a property -->
<div class="modal fade" id="assign-editors-modal" tabindex="-1" role="dialog" aria-labelledby="assign-editors-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assign-editors-modal-label">Assign Managers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assign-editors-form" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="POST"> 
                    <div class="form-group">
                        <label for="assign-editors-select">Select Editors</label>
                        <select class="form-control" id="assign-editors-select" multiple 
                            data-live-search="true" placeholder="please type to search" data-live-search-placeholder="search..."
                            name="editor_ids[]"
                        >
                            <option value="" hidden disabled>Select Editors</option>
                            <?php foreach ($users as $user) { 
                                if(!in_array("administrator", $user->roles) && $user->ID != $user_id) { ?>
                                    <option value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
                            <?php } } ?>
                        </select>
                    </div>  
                    <input type="hidden" id="post_meta" name="post_meta">
                    <input type="hidden" id="user_id" name="user_id">
                    <input type="hidden" id="post_id" name="post_id">
                    <input type="hidden" id="author_id" name="author_id"> 
                    
                    <!-- Set the button to right align -->
                    <div class="text-right">
                        <button type="submit" class="btn btn-success">Update Managers</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    const users = <?php echo json_encode($users); ?>;
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('#assign-editors-form');
        const assignEditors = document.querySelectorAll('[assign-editors]');
        assignEditors.forEach(editor => {
            editor.addEventListener('click', (e) => {
                e.preventDefault();
                
                const data = JSON.parse(editor.getAttribute('assign-editors'));
                const editorIds = JSON.parse(data?.editor_ids);

                form.querySelector('#user_id').value = data?.user_id;
                form.querySelector('#post_id').value = data?.post_id;
                form.querySelector('#author_id').value = data?.post_author;

                jQuery('#assign-editors-modal').modal('show');
                jQuery('#assign-editors-select').selectpicker({
                    multiple: true,
                    search: true
                }).selectpicker('val', editorIds);

                console.log({editorIds,data});
            });
        });

        // use selectpicker for the select element and enable multiple selection with search
        jQuery('#assign-editors-select').selectpicker({
            multiple: true,
            search: true,
        });

        jQuery(document).on('click', '[alert-on-click]', function(e) {
            e.preventDefault();
            const url = jQuery(this).attr('href');

            if(confirm('Are you sure you want to remove this editor?')) {
                window.location.href = url;
            }
        });
    });
</script>

<style>
    /* Add this CSS to style the selected options and the cross icon */
    .selectpicker .dropdown-menu .selected {
        background-color: #f0f0f0; /* Light gray background */
        position: relative; /* Positioning for the icon */
    }

    .selectpicker .dropdown-menu .selected .remove-icon {
        position: absolute;
        right: 5px; /* Adjust as needed */
        top: 50%;
        transform: translateY(-50%);
        border: 1px solid #ccc; /* Border for the icon */
        border-radius: 50%; /* Rounded border */
        padding: 2px; /* Padding for the icon */
        cursor: pointer; /* Pointer cursor on hover */
        background-color: white; /* Background for the icon */
    }
</style>

<?php get_footer(); ?>