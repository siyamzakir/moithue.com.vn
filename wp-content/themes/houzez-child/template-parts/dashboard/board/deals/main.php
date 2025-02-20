<?php
/**
 * @developed By AppsZone
 * Starting 19 Feb. 2025 - 12:46AM
 * @github https://github.com/appszone
 * @website https://appszone.co.id
 * 
 * Start From Now
 */

// make sure general user has permission by self from env or login as admin
$has_permission = houzez_is_admin() || DB::DEALS_LEADS_MANAGE_BY_SELF;
$user_id = (int) ($has_permission ? 0 : get_current_user_id());

$deal_group = filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'active';
$items_per_page = filter_input(INPUT_GET, 'records', FILTER_VALIDATE_INT, ['options' => ['default' => 10, 'min_range' => 1]]);
$page = filter_input(INPUT_GET, 'cpage', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);

// get all deals
$deals = DB::getAllDeals($user_id, $deal_group, $items_per_page, $page);

$hpage = 'deals';
$tabs = ['active', 'won', 'lost'];

// get total deals by group
$active_deals = DB::getTotalDealsByGroup($user_id, $tabs[0]);
$won_deals = DB::getTotalDealsByGroup($user_id, $tabs[1]);
$lost_deals = DB::getTotalDealsByGroup($user_id, $tabs[2]);

$dashboard_crm = houzez_get_template_link_2('template/user_dashboard_crm.php');
$activated_tab = filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'active';

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
                <a class="btn btn-primary open-close-deal-panel" href="#"><?php esc_html_e('Add New Deal', 'houzez'); ?></a>
            </div>
        </div>
    </div>
</header>

<?php if($deals && !empty($deals)): ?>
    <section class="dashboard-content-wrap deals-main-wrap">
        <!-- Use Deals Statistics -->
        <div class="dashboard-content-inner-wrap">
            <div class="dashboard-content-block dashboard-statistic-block">
                <h3>
                    <i class="houzez-icon icon-sign-badge-circle mr-2 primary-text"></i> 
                    <?php esc_html_e('Deals', 'houzez'); ?>
                </h3>
                <div class="d-flex align-items-center sm-column">
                    <!-- Doughnut chart for visual representation of deals -->
                    <div class="statistic-doughnut-chart">
                        <canvas id="deals-doughnut-chart" 
                                data-active="<?php echo intval($active_deals); ?>" 
                                data-won="<?php echo intval($won_deals); ?>" 
                                data-lost="<?php echo intval($lost_deals); ?>" 
                                width="100" height="100">
                        </canvas>
                    </div>
                    <!-- Data display for each deal category -->
                    <div class="doughnut-chart-data flex-fill">
                        <ul class="list-unstyled">
                            <li class="stats-data-3">
                                <i class="houzez-icon icon-sign-badge-circle mr-1"></i> 
                                <strong><?php esc_html_e('Active', 'houzez'); ?></strong> 
                                <span><?php echo number_format_i18n($active_deals); ?> <small><?php esc_html_e('Deals', 'houzez'); ?></small></span>
                            </li>
                            <li class="stats-data-4">
                                <i class="houzez-icon icon-sign-badge-circle mr-1"></i> 
                                <strong><?php esc_html_e('Won', 'houzez'); ?></strong> 
                                <span><?php echo number_format_i18n($won_deals); ?> <small><?php esc_html_e('Deals', 'houzez'); ?></small></span>
                            </li>
                            <li class="stats-data-1">
                                <i class="houzez-icon icon-sign-badge-circle mr-1"></i> 
                                <strong><?php esc_html_e('Lost', 'houzez'); ?></strong> 
                                <span><?php echo number_format_i18n($lost_deals); ?> <small><?php esc_html_e('Deals', 'houzez'); ?></small></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="deals-table-wrap">

            <ul class="nav nav-pills deals-nav-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active-deals <?= $activated_tab == $tabs[0] ? 'active' : ''; ?>" 
                        href="<?= esc_url(add_query_arg(['tab'=> $tabs[0] , 'hpage'=> $hpage], $dashboard_crm)); ?>"
                    >
                        <?= "Active Deals ({$active_deals})"; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link won-deals <?= $activated_tab == $tabs[1] ? 'active' : ''; ?>" 
                        href="<?= esc_url(add_query_arg(['tab'=> $tabs[1] , 'hpage'=> $hpage], $dashboard_crm)); ?>"
                    >
                        <?= "Won Deals ({$won_deals})"; ?>
                    </a>
                </li>
                <li class="nav-item lost-deals">
                    <a class="nav-link <?= $activated_tab == $tabs[2] ? 'active' : ''; ?>" 
                        href="<?= esc_url(add_query_arg(['tab'=> $tabs[2] , 'hpage'=> $hpage], $dashboard_crm)); ?>"
                    >
                        <?= "Lost Deals ({$lost_deals})"; ?>
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