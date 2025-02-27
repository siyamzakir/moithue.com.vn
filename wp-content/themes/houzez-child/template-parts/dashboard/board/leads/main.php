<?php 

/**
 * @developed By AppsZone
 * Starting 19 Feb. 2025 - 12:46AM
 * @github https://github.com/appszone
 * @website https://appszone.co.id
 * 
 * Start From Now
 */

global $dashboard_crm, $hpage, $name, $phone, $start_date, $end_date, $referrer, $reset_url;

// handle message from response
$response = ['type' => '', 'message' => ''];
$page_root_uri = explode('?', $_SERVER['REQUEST_URI'])[0];

// handle lock status
if(isset($_GET['type']) && $_GET['type'] == 'lock_status') {
    $lead_id = isset($_GET['lead_id']) ? intval($_GET['lead_id']) : 0;
    $is_locked = isset($_GET['is_locked']) ? intval($_GET['is_locked']) : 0;

    $updater = DB::updateByColumns(DB::HOUZEZ_CRM_LEADS, ['lead_id' => $lead_id], ['is_locked' => $is_locked ? 0 : 1], false);

    if($updater) {
        $response['type'] = 'success';
        $response['message'] = 'Lead locked updated successfully';
    } else {
        $response['type'] = 'danger';
        $response['message'] = 'Failed update to lock lead';
    }
   
    echo Section::echoDefaultUrl("?hpage=leads");
}

// if general user has permission by self from env or login as admin
$has_permission = houzez_is_admin() || DB::DEALS_LEADS_MANAGE_BY_SELF;
$user_id = $has_permission ? 0 : get_current_user_id();

$hpage = filter_input(INPUT_GET, 'hpage', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
$keyword = sanitize_text_field(trim(filter_input(INPUT_GET, 'keyword', FILTER_SANITIZE_FULL_SPECIAL_CHARS)));
$page = filter_input(INPUT_GET, 'cpage', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$apply_filters = sanitize_text_field(trim(filter_input(INPUT_GET, 'apply_filters', FILTER_SANITIZE_FULL_SPECIAL_CHARS)));
$items_per_page = filter_input(INPUT_GET, 'records', FILTER_VALIDATE_INT, ['options' => ['default' => 10, 'min_range' => 1]]);

// Get and sanitize filter parameters
$name = filter_input(INPUT_GET, 'lead_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
$phone = filter_input(INPUT_GET, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
$start_date = filter_input(INPUT_GET, 'start_date', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
$end_date = filter_input(INPUT_GET, 'end_date', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';
$referrer = filter_input(INPUT_GET, 'referrer', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '';

// get all leads
$leads = DB::getLeads($user_id, compact('keyword','name', 'phone', 'start_date', 'end_date', 'referrer'), $items_per_page, $page);

/**
 * End Handle Lock Status
 * @developed By AppsZone
*/
// dashboard crm link
$dashboard_crm = houzez_get_template_link_2('template/user_dashboard_crm.php');

$reset_url = esc_url(add_query_arg('hpage', $hpage, $dashboard_crm));
$import_link = add_query_arg( 'hpage', 'import-leads', $dashboard_crm );

?>

<!-- dashboard-header-wrap end -->
<header class="header-main-wrap dashboard-header-main-wrap">
    <div class="dashboard-header-wrap">
        <div class="d-flex align-items-center">
            <div class="dashboard-header-left flex-grow-1">
                <h1><?php echo houzez_option('dsh_leads', 'Leads'); ?></h1>
            </div>
            
            <!-- dashboard-header-left  -->
            <div class="dashboard-header-right">
                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#leadsFilterModal">
                    <i class="houzez-icon icon-search mr-1"></i> <?php esc_html_e('Filter Leads', 'houzez'); ?>
                </button>
                <?php if($apply_filters == 'true'): ?>
                    <a type="button" class="btn btn-dark ml-3" href="<?= $reset_url; ?>">
                        <?php esc_html_e('Reset Filters', 'houzez'); ?>
                    </a>
                <?php endif; ?>
                <button class="btn btn-primary open-close-slide-panel ml-3">
                    <?php esc_html_e('Add New Lead', 'houzez'); ?>
                </button>
            </div>
            <!-- dashboard-header-right -->
        </div>
        <!-- d-flex -->

        <!-- Handle notification messages with close button -->
        <?php if(!empty($response['type']) && !empty($response['message'])) { ?>
            <div class="alert alert-<?php echo esc_attr($response['type']); ?> alert-dismissible fade show d-flex justify-content-between" role="alert"
                style="margin: 20px 0 -55px 0px;"
            >
                <div><?php echo esc_attr($response['message']); ?></div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-top: -4px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>
    </div>

    <!-- Leads Filtering -->
</header>

<!-- Leads Filter Form -->
<?php get_template_part('template-parts/dashboard/board/leads/leads-filters'); ?>

<!-- Check if exists leads -->
<?php if($leads && !empty($leads)): ?>
    <!-- .header-main-wrap -->
    <section class="dashboard-content-wrap leads-main-wrap">
        <div class="dashboard-content-inner-wrap">
            <?php get_template_part('template-parts/dashboard/statistics/statistic-leads'); ?>
            
            <div class="dashboard-content-block-wrap">
                <div class="dashboard-tool-block">
                    <div class="dashboard-tool-buttons-block">
                        <div class="dashboard-tool-button">
                            <button onclick="window.location.href='<?php echo esc_url($import_link);?>';" class="btn btn-primary-outlined"><?php echo esc_html__( 'Import', 'houzez' ); ?></button>
                        </div>
                        <div class="dashboard-tool-button">
                            <button id="export-leads" class="btn btn-primary-outlined"><span class="btn-loader houzez-loader-js"></span><?php esc_html_e( 'Export', 'houzez' ); ?>
                            </button>
                        </div>
                        <?php if($has_permission) { ?>
                            <div class="dashboard-tool-button">
                                <button id="bulk-delete-leads" class="btn btn-grey-outlined"><?php echo esc_html__( 'Delete', 'houzez' ); ?></button>
                            </div>
                        <?php } ?>
                        <div class="dashboard-tool-button">
                            <div class="btn"><i class="houzez-icon icon-single-neutral-circle mr-2 grey"></i>
                                <?php echo esc_attr($leads['data']['total_records']); ?> <?php esc_html_e('Results Found', 'houzez'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- dashboard-tool-buttons-block -->
                    <div class="dashboard-tool-search-block">
                        <div class="dashboard-crm-search-wrap">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <div class="dashboard-crm-search">
                                        <form name="search-leads" method="get" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                                            <input type="hidden" name="hpage" value="<?php echo esc_attr($hpage); ?>">
                                        <div class="d-flex">
                                            <div class="form-group">
                                                <div class="search-icon">
                                                    <input name="keyword" type="text" value="<?php echo esc_attr($keyword); ?>" class="form-control" placeholder="<?php echo esc_html__('Search', 'houzez'); ?>">
                                                </div><!-- search-icon -->
                                            </div><!-- form-group -->
                                            <button type="submit" class="btn btn-search btn-secondary"><?php echo esc_html__( 'Search', 'houzez' ); ?></button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- dashboard-crm-search-wrap -->
                    </div>
                    <!-- dashboard-tool-search-block -->
                </div>
                <!-- dashboard-tool-block -->

                <?php
                $dashboard_crm = houzez_get_template_link_2('template/user_dashboard_crm.php');
            
                if(!empty($leads['data']['results'])) { ?>

                    <table class="dashboard-table table-lined table-hover responsive-table">
                        <thead>
                            <tr>
                                <?php if($has_permission) { ?>
                                    <th>
                                        <label class="control control--checkbox">
                                            <input type="checkbox" class="checkbox-delete" id="leads_select_all" name="leads_multicheck">
                                            <span class="control__indicator"></span>
                                        </label>
                                    </th>
                                <?php } ?>
                                <th><?php esc_html_e('Name', 'houzez'); ?></th>
                                <th><?php esc_html_e('Email', 'houzez'); ?></th>
                                <th><?php esc_html_e('Phone', 'houzez'); ?></th>
                                <th><?php esc_html_e('Type', 'houzez'); ?></th>
                                <th><?php esc_html_e('Date', 'houzez'); ?></th>
                                <th class="action-col"><?php esc_html_e('Actions', 'houzez'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leads['data']['results'] as $result) { 
                                $detail_link = add_query_arg(
                                    array(
                                        'hpage' => 'lead-detail',
                                        'lead-id' => $result->lead_id,
                                        'tab' => 'enquires',
                                    ), $dashboard_crm
                                );

                                $datetime = $result->time;

                                $datetime_unix = strtotime($datetime);
                                $get_date = houzez_return_formatted_date($datetime_unix);
                                $get_time = houzez_get_formatted_time($datetime_unix);
                            ?>

                                <tr>
                                    <?php if($has_permission) { ?>
                                        <td>
                                            <label class="control control--checkbox">
                                                <input type="checkbox" class="checkbox-delete lead-bulk-delete" name="lead-bulk-delete[]" value="<?php echo intval($result->lead_id); ?>">
                                                <span class="control__indicator"></span>
                                            </label>
                                        </td>
                                    <?php } ?>
                                    <td class="table-nowrap" data-label="<?php esc_html_e('Name', 'houzez'); ?>">
                                        <?php echo esc_attr($result->display_name); ?>
                                    </td>
                                    <td data-label="<?php esc_html_e('Email', 'houzez'); ?>">
                                        <a href="mailto:<?php echo esc_attr($result->email); ?>">
                                            <strong><?php echo esc_attr($result->email); ?></strong>
                                        </a>
                                    </td>
                                    <td data-label="<?php esc_html_e('Phone', 'houzez'); ?>">
                                        <?php echo esc_attr($result->mobile); ?>
                                    </td>
                                    <td data-label="<?php esc_html_e('Type', 'houzez'); ?>">
                                        <?php 
                                        if( $result->type ) {
                                            $type = stripslashes($result->type);
                                            $type = htmlentities($type);
                                            echo esc_attr($type); 
                                        }?>
                                    </td>
                                    <td class="table-nowrap" data-label="<?php esc_html_e('Date', 'houzez'); ?>">
                                        <?php echo esc_attr($get_date); ?><br>
                                        <?php echo esc_html__('at', 'houzez'); ?> <?php echo esc_attr($get_time); ?>
                                    </td>
                                    <td>
                                        <div class="dropdown property-action-menu">
                                            <button class="btn btn-primary-outlined dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <?php esc_html_e('Actions', 'houzez'); ?>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="<?php echo esc_url($detail_link); ?>"><?php esc_html_e('Details', 'houzez'); ?></a>

                                                <!-- Lock Lead Customized by AppsZone -->
                                                <a class="lock-lead dropdown-item"
                                                    data-id="<?php echo intval($result->lead_id)?>"
                                                    href='<?= "{$page_root_uri}?hpage=leads&type=lock_status&lead_id={$result->lead_id}&is_locked={$result->is_locked}"; ?>'
                                                >
                                                    <?php esc_html_e($result->is_locked ? 'Unlock Now' : 'Lock Now', 'houzez'); ?>
                                                </a>
                                                
                                                <?php if(houzez_is_admin() || DB::DEALS_LEADS_MANAGE_BY_SELF) { ?>
                                                    <a class="edit-lead dropdown-item open-close-slide-panel" data-id="<?php echo intval($result->lead_id)?>" href="#"><?php esc_html_e('Edit', 'houzez'); ?></a>
                                                    <a href="#" class="delete-lead dropdown-item" data-id="<?php echo intval($result->lead_id); ?>" data-nonce="<?php echo wp_create_nonce('delete_lead_nonce') ?>"><?php esc_html_e('Delete', 'houzez'); ?></a>
                                                <?php } ?>
                                                <!-- End Lock Lead Customized by AppsZone -->
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php
                } else { ?>
                    <div class="dashboard-content-block">

                        <?php if( $keyword ) { 
                            esc_html_e("No Result Found", 'houzez');
                        } else { ?>
                        <?php esc_html_e("You don't have any contact at this moment.", 'houzez'); ?> <a class="open-close-slide-panel" href="#"><strong><?php esc_html_e('Add New Lead', 'houzez'); ?></strong></a>
                        <?php } ?>
                    </div><!-- dashboard-content-block -->
                <?php } ?>
            

            </div><!-- dashboard-content-block-wrap -->
        </div><!-- dashboard-content-inner-wrap -->

        <div class="leads-pagination-wrap">
            <div class="leads-pagination-item-count">
                <?php get_template_part('template-parts/dashboard/board/records-html'); ?>
            </div>

            <?php
            $total_pages = ceil($leads['data']['total_records'] / $leads['data']['items_per_page']);
            $current_page = $leads['data']['page'];
            houzez_crm_pagination($total_pages, $current_page);
            ?>

        </div> <!-- leads-pagination-wrap -->

    </section>
    <!-- dashboard-content-wrap -->
<?php else: ?>
    <!-- use alert and alert danger -->
    <section class="dashboard-content-wrap leads-main-wrap">
        <div class="dashboard-content-inner-wrap">
            <div class="alert alert-danger">
                <?php esc_html_e("You don't have any contact at this moment.", 'houzez'); ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="dashboard-side-wrap">
    <?php get_template_part('template-parts/dashboard/side-wrap'); ?>
</section>
