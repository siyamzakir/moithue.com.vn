<?php
global $all_enquires;
// make sure general user has permission by self from env or login as admin
$has_permission = houzez_is_admin() || DB::DEALS_LEADS_MANAGE_BY_SELF;
$user_id = (int) ($has_permission ? 0 : get_current_user_id());

$items_per_page = filter_input(INPUT_GET, 'records', FILTER_VALIDATE_INT, ['options' => ['default' => 10]]);
$page = filter_input(INPUT_GET, 'cpage', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);

$hpage = filter_input(INPUT_GET, 'hpage', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$lead_id = filter_input(INPUT_GET, 'lead-id', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 0;
$keyword = filter_input(INPUT_GET, 'keyword', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

// get All Inquiries
$all_enquires = DB::getAllInquiries($user_id, $keyword, $lead_id, $items_per_page, $page);

?>
<header class="header-main-wrap dashboard-header-main-wrap">
    <div class="dashboard-header-wrap">
        <div class="d-flex align-items-center">
            <!-- dashboard-header-left -->
            <div class="dashboard-header-left flex-grow-1">
                <h1><?php echo houzez_option('dsh_inquiries', 'Inquiries'); ?></h1>         
            </div>
            
            <!-- dashboard-header-right -->
            <div class="dashboard-header-right">
                <button class="btn btn-primary open-close-enquiry-panel"><?php esc_html_e('Add New Inquiry', 'houzez'); ?></button>
            </div>
        </div>
    </div>
</header>

<?php if($all_enquires && !empty($all_enquires)) : ?>
    <section class="dashboard-content-wrap">
        <div class="dashboard-content-inner-wrap">
            <div class="dashboard-content-block-wrap">

                <div class="dashboard-tool-block">
                    <!-- dashboard-tool-buttons-block -->
                    <div class="dashboard-tool-buttons-block">
                        <div class="dashboard-tool-button">
                            <button id="export-inquiries" class="btn btn-primary-outlined"><span class="btn-loader houzez-loader-js"></span><?php esc_html_e( 'Export', 'houzez' ); ?>
                            </button>
                        </div>
                        <?php if ($has_permission) : ?>
                            <div class="dashboard-tool-button">
                                <button id="enquiry_delete_multiple" class="btn btn-grey-outlined"><?php echo esc_html__( 'Delete', 'houzez' ); ?></button>
                            </div>
                        <?php endif; ?>
                        <div class="dashboard-tool-button">
                            <div class="btn"><i class="houzez-icon icon-single-neutral-circle mr-2 grey"></i>
                                <?php echo esc_attr($all_enquires['data']['total_records']); ?> <?php esc_html_e('Inquiries found', 'houzez'); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-tool-search-block">
                        <div class="dashboard-crm-search-wrap">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <div class="dashboard-crm-search">
                                        <form name="search-inquiries" method="get" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                                            <input type="hidden" name="hpage" value="<?php echo esc_attr($hpage); ?>">
                                        <div class="d-flex">
                                            <div class="form-group">
                                                <div class="search-icon">
                                                    <input name="keyword" type="text" value="<?php echo esc_attr($keyword); ?>" class="form-control" placeholder="<?php echo esc_html__('Inquiry Type', 'houzez'); ?>">
                                                </div><!-- search-icon -->
                                            </div><!-- form-group -->
                                            <button type="submit" class="btn btn-search btn-secondary"><?php echo esc_html__( 'Search', 'houzez' ); ?></button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                
                </div><!-- dashboard-tool-block -->
            
                <?php get_template_part('template-parts/dashboard/board/enquires/enquires'); ?>

            </div><!-- dashboard-content-block-wrap -->
        </div><!-- dashboard-content-inner-wrap -->

        <div class="leads-pagination-wrap">
            <div class="leads-pagination-item-count">
                <?php get_template_part('template-parts/dashboard/board/records-html'); ?>
            </div>

            <?php
            $total_pages = ceil($all_enquires['data']['total_records'] / $all_enquires['data']['items_per_page']);
            $current_page = $all_enquires['data']['page'];
            houzez_crm_pagination($total_pages, $current_page);
            ?>
        </div> <!-- leads-pagination-wrap -->

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