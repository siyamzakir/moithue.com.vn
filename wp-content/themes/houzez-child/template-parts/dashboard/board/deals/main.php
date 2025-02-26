<?php
/**
 * @developed By AppsZone
 * Starting 19 Feb. 2025 - 12:46AM
 * @github https://github.com/appszone
 * @website https://appszone.co.id
 * 
 * Start From Now
 */
@ini_set('display_errors', 1);

global $dashboard_crm, $has_permission, $user_id, $hpage, $deal_group, $deals, $submit_filters, $active_deals, $won_deals, $lost_deals, $property_id, $lead_id, $agent_id, $deal_title, $next_action, $due_date, $next_actions, $lead_email, $lead_mobile, $tabs, $status, $reset_url;

$hpage = 'deals';
$tabs = ['active', 'won', 'lost'];
$dashboard_crm = houzez_get_template_link_2('template/user_dashboard_crm.php');
$deal_group = filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? $tabs[0];

// make sure general user has permission by self from env or login as admin
$has_permission = houzez_is_admin() || DB::DEALS_LEADS_MANAGE_BY_SELF;
$user_id = (int) ($has_permission ? 0 : get_current_user_id());

$submit_filters = filter_input(INPUT_GET, 'submit_filters', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$page = filter_input(INPUT_GET, 'cpage', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$items_per_page = filter_input(INPUT_GET, 'records', FILTER_VALIDATE_INT, ['options' => ['default' => 10, 'min_range' => 1]]);

$property_id = filter_input(INPUT_GET, 'property_id', FILTER_VALIDATE_INT);
$lead_id = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);
$agent_id = filter_input(INPUT_GET, 'agent_id', FILTER_VALIDATE_INT);

$deal_title = filter_input(INPUT_GET, 'deal_title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$next_action = filter_input(INPUT_GET, 'next_action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$due_date = filter_input(INPUT_GET, 'due_date', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$lead_email = filter_input(INPUT_GET, 'lead_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$lead_mobile = filter_input(INPUT_GET, 'lead_mobile', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$deals = DB::getDeals(
    $user_id, 
    compact('property_id', 'lead_id', 'agent_id', 'deal_title', 'next_action', 'due_date', 'deal_group', 'lead_email', 'lead_mobile', 'status'),
    $items_per_page, 
    $page
); 

$reset_url = esc_url(add_query_arg(['tab'=> $tabs[0] , 'hpage'=> $hpage], $dashboard_crm));

// echo "<script>console.log(".json_encode(compact('has_permission', 'user_id', 'active_deals', 'won_deals', 'lost_deals', 'property_id', 'lead_id', 'agent_id', 'deal_title', 'next_action', 'due_date', 'lead_email', 'lead_mobile', 'deals')).");</script>";


/** * End Main Functionality but utilities going on */


/** * Start Houzez's Default Functionality */

?>

<header class="header-main-wrap dashboard-header-main-wrap">
    <div class="dashboard-header-wrap">
        <div class="d-flex align-items-center">
            <!-- dashboard-header-left -->
            <div class="dashboard-header-left flex-grow-1">
                <h1><?php echo houzez_option('dsh_deals', 'Deals'); ?></h1>         
            </div>

            <!-- dashboard-header-right -->
            <div class="dashboard-header-right">
                <button type="button" class="btn btn-warning mr-3" data-toggle="modal" data-target="#dealsFilterModal">
                    <i class="houzez-icon icon-search mr-1"></i> <?php esc_html_e('Filter Deals', 'houzez'); ?>
                </button>

                <?php if($submit_filters == 'true'): ?>
                    <a class="btn btn-dark mr-3" href="<?= $reset_url; ?>">
                        <?php esc_html_e('Reset Filters', 'houzez'); ?>
                    </a>
                <?php endif; ?>

                <button class="btn btn-primary open-close-deal-panel">
                    <?php esc_html_e('Add New Deal', 'houzez'); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Advanced Apply Filtering -->
</header>

<!-- Advanced Apply Filtering -->
<?php get_template_part('template-parts/dashboard/board/deals/deals-filters'); ?>

<?php if($deals && !empty($deals)): ?>
    <section class="dashboard-content-wrap deals-main-wrap">
        <!-- if total_records is garter then 0 -->
        <?php if($deals['data']['total_records'] <= 0): ?>
            <div class="alert alert-danger">
                <?php esc_html_e("No deals found.", 'houzez'); ?>
            </div>
        <?php endif; ?>

        <!-- Use Deals Statistics -->
        <div class="dashboard-content-inner-wrap">
            <div class="dashboard-content-block dashboard-statistic-block">
                <h3>
                    <i class="houzez-icon icon-sign-badge-circle mr-2 primary-text"></i> 
                    Deals
                </h3>
                <div class="d-flex align-items-center sm-column">
                    <!-- Doughnut chart for visual representation of deals -->
                    <div class="statistic-doughnut-chart">
                        <canvas id="deals-doughnut-chart" 
                                data-active="<?= $deals['group_counts'][$tabs[0]]; ?>" 
                                data-won="<?= $deals['group_counts'][$tabs[1]]; ?>" 
                                data-lost="<?= $deals['group_counts'][$tabs[1]]; ?>" 
                                width="100" height="100">
                        </canvas>
                    </div>
                    <!-- Data display for each deal category -->
                    <div class="doughnut-chart-data flex-fill">
                        <ul class="list-unstyled">
                            <li class="stats-data-3">
                                <i class="houzez-icon icon-sign-badge-circle mr-1"></i> 
                                <strong><?php esc_html_e('Active', 'houzez'); ?></strong> 
                                <span><?= number_format_i18n($deals['group_counts'][$tabs[0]]); ?> <small><?php esc_html_e('Deals', 'houzez'); ?></small></span>
                            </li>
                            <li class="stats-data-4">
                                <i class="houzez-icon icon-sign-badge-circle mr-1"></i> 
                                <strong><?php esc_html_e('Won', 'houzez'); ?></strong> 
                                <span><?= number_format_i18n($deals['group_counts'][$tabs[1]]); ?> <small><?php esc_html_e('Deals', 'houzez'); ?></small></span>
                            </li>
                            <li class="stats-data-1">
                                <i class="houzez-icon icon-sign-badge-circle mr-1"></i> 
                                <strong><?php esc_html_e('Lost', 'houzez'); ?></strong> 
                                <span><?= number_format_i18n($deals['group_counts'][$tabs[2]]); ?> <small><?php esc_html_e('Deals', 'houzez'); ?></small></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="deals-table-wrap">
            <ul class="nav nav-pills deals-nav-tab" id="deals-nav-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active-deals <?= $deal_group == $tabs[0] ? 'active' : ''; ?>" 
                        href="<?= esc_url(add_query_arg(['tab'=> $tabs[0] , 'hpage'=> $hpage], $dashboard_crm)); ?>"
                        action="<?= $tabs[0]; ?>"
                    >
                        <?= "Active Deals ({$deals['group_counts'][$tabs[0]]})"; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link won-deals <?= $deal_group == $tabs[1] ? 'active' : ''; ?>" 
                        href="<?= esc_url(add_query_arg(['tab'=> $tabs[1] , 'hpage'=> $hpage], $dashboard_crm)); ?>"
                        action="<?= $tabs[1]; ?>"
                    >
                        <?= "Won Deals ({$deals['group_counts'][$tabs[1]]})"; ?>
                    </a>
                </li>
                <li class="nav-item lost-deals">
                    <a class="nav-link <?= $deal_group == $tabs[2] ? 'active' : ''; ?>" 
                        href="<?= esc_url(add_query_arg(['tab'=> $tabs[2] , 'hpage'=> $hpage], $dashboard_crm)); ?>"
                        action="<?= $tabs[2]; ?>"
                    >
                        <?= "Lost Deals ({$deals['group_counts'][$tabs[2]]})"; ?>
                    </a>
                </li>
            </ul>
            <div class="deal-content-wrap p-0">
                <table class="dashboard-table table-lined deals-table responsive-table">
                    <thead>
                        <tr>
                            <th class="table-nowrap"><?php esc_html_e('Title', 'houzez'); ?></th>
                            <th class="table-nowrap"><?php esc_html_e('Property', 'houzez'); ?></th>
                            <th class="table-nowrap"><?php esc_html_e('Contact Name', 'houzez'); ?></th>
                            <?php if( houzez_is_admin() ) { ?>
                                <th class="table-nowrap"><?php esc_html_e('Agent', 'houzez'); ?></th>
                            <?php } ?>
                            <th class="table-nowrap"><?php esc_html_e('Status', 'houzez'); ?></th>
                            <th class="table-nowrap"><?php esc_html_e('Next Action', 'houzez'); ?></th>
                            <th class="table-nowrap"><?php esc_html_e('Action Due Date', 'houzez'); ?></th>
                            <th class="table-nowrap"><?php esc_html_e('Deal Value', 'houzez'); ?></th>
                            <th class="table-nowrap"><?php esc_html_e('Last Contact Date', 'houzez'); ?></th>
                            <th class="table-nowrap"><?php esc_html_e('Phone', 'houzez'); ?></th>
                            <th class="table-nowrap"><?php esc_html_e('Email', 'houzez'); ?></th>
                            <?php if($has_permission) { ?>
                                <th class="table-nowrap"><?php esc_html_e('Actions', 'houzez'); ?></th>
                            <?php } ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php 
                            global $deal_data;
                            foreach ($deals['data']['results'] as $deal_data) { 
                                get_template_part( 'template-parts/dashboard/board/deals/deal-item' );
                            }
                        ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="5">
                                <?php get_template_part('template-parts/dashboard/board/records-html'); ?>
                            </td>
                            
                            <td colspan="2" class="text-right no-wrap">
                                <div class="leads-pagination-wrap">
                                    <?php
                                        $total_pages = ceil($deals['data']['total_records'] / $deals['data']['items_per_page']);
                                        $current_page = $deals['data']['page'];
                                        houzez_crm_pagination($total_pages, $current_page);
                                    ?>
                                </div> <!-- leads-pagination-wrap -->
                            </td>
                        </tr>
                    </tfoot>
                </table><!-- dashboard-table -->
            </div><!-- dashboard-content-block -->

        </div> 
    </section> 
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
 