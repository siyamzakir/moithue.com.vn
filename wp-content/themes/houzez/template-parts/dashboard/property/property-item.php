<?php
global $post, $houzez_local;

$post_id = get_the_ID();
$submit_link = houzez_dashboard_add_listing();
$listings_page = houzez_dashboard_listings();
$edit_link = add_query_arg('edit_property', $post_id, $submit_link);
$delete_link = add_query_arg('property_id', $post_id, $listings_page);
$paid_submission_type = houzez_option('enable_paid_submission');
$property_status = get_post_status($post->ID);
$payment_status = get_post_meta($post->ID, 'fave_payment_status', true);

$payment_page = houzez_get_template_link('template/template-payment.php');
$insights_page = houzez_get_template_link_2('template/user_dashboard_insight.php');
$payment_page_link = add_query_arg('prop-id', $post_id, $payment_page);
$payment_page_link_featured = add_query_arg('upgrade_id', $post_id, $payment_page);
$insights_page_link = add_query_arg('listing_id', $post_id, $insights_page);
$fave_featured = get_post_meta($post->ID, 'fave_featured', true);

$is_user_can_manage = houzez_is_admin() || houzez_is_editor();
$put_on_hold_class = ($paid_submission_type == 'membership') ? 'put-on-hold-package' : 'put-on-hold';

// Status Badge
$status_badge = '';
$status_text = '';

switch ($property_status) {
    case 'publish':
        $status_text = esc_html__('Approved', 'houzez');
        $status_badge = "<span class='badge badge-success'>{$status_text}</span>";
        break;
    case 'on_hold':
        $status_text = esc_html__('On Hold', 'houzez');
        $status_badge = "<span class='badge badge-info'>{$status_text}</span>";
        break;
    case 'houzez_sold':
        $status_text = esc_html__('Sold', 'houzez');
        $status_badge = "<span class='badge badge-danger'>{$status_text}</span>";
        break;
    case 'pending':
        $status_text = esc_html__('Pending', 'houzez');
        $status_badge = "<span class='badge badge-warning'>{$status_text}</span>";
        break;
    case 'expired':
        $status_text = esc_html__('Expired', 'houzez');
        $status_badge = "<span class='badge badge-danger'>{$status_text}</span>";
        break;
    case 'disapproved':
        $status_text = esc_html__('Disapproved', 'houzez');
        $status_badge = "<span class='badge badge-danger'>{$status_text}</span>";
        break;
    case 'draft':
        $status_text = esc_html__('Draft', 'houzez');
        $status_badge = "<span class='badge badge-dark'>{$status_text}</span>";
        break;
}

// Payment Status Label
$payment_status_label = '';
if ($property_status != 'expired' && $property_status != 'disapproved') {
    if ($paid_submission_type != 'no' && $paid_submission_type != 'membership' && $paid_submission_type != 'free_paid_listing') {
        $payment_status_label = ($payment_status == 'paid') 
            ? '<span class="label property-payment-status">' . esc_html__('PAID', 'houzez') . '</span>' 
            : '<span class="label property-payment-status">' . esc_html__('NOT PAID', 'houzez') . '</span>';
    }
}
?>
<tr>
    <td class="property-table-thumbnail" data-label="<?php echo esc_html__('Thumbnail', 'houzez'); ?>">
        <div class="table-property-thumb">
            <?php if (houzez_user_role_by_post_id($post_id) != 'administrator') echo $payment_status_label; ?>
            <a href="<?php echo esc_url(get_permalink()); ?>">
                <?php
                if (has_post_thumbnail()) {
                    the_post_thumbnail(array('100', '75'));
                } else {
                    echo '<img src="http://via.placeholder.com/100x75">';
                }
                ?>
            </a>    
        </div>
    </td>
    
    <td class="property-table-address" data-label="<?php echo esc_html__('Title', 'houzez'); ?>">
        <a href="<?php echo esc_url(get_permalink()); ?>"><strong><?php the_title(); ?></strong></a><br>
        <?php echo houzez_get_listing_data('property_map_address'); ?><br>
        <?php if (houzez_user_role_by_post_id($post_id) != 'administrator' && $property_status == 'publish') { ?>
            <?php if ($paid_submission_type == 'membership') { ?>
                <span class="expiration_date">
                    <?php echo esc_html__('Expiration:', 'houzez'); ?> <?php houzez_listing_expire(); ?>
                    <?php houzez_featured_listing_expire(); ?><br>
                </span>
            <?php } elseif ($paid_submission_type != 'no' && houzez_option('per_listing_expire_unlimited')) { ?>
                <span class="expiration_date">
                    <?php echo esc_html__('Expiration:', 'houzez'); ?> <?php houzez_listing_expire(); ?>
                    <?php houzez_featured_listing_expire(); ?><br>
                </span>
            <?php } ?>
        <?php } ?>
    </td>

    <td><?php echo $status_badge; ?></td>

    <td class="property-table-type" data-label="<?php echo esc_html__('Type', 'houzez'); ?>">
        <?php echo houzez_taxonomy_simple('property_type'); ?>&nbsp    
    </td>

    <td class="property-table-status" data-label="<?php echo esc_html__('Status', 'houzez'); ?>">
        <?php echo houzez_taxonomy_simple('property_status'); ?>&nbsp
    </td>

    <td class="property-table-price" data-label="<?php echo esc_html__('Price', 'houzez'); ?>">
        <?php houzez_property_price_admin(); ?>&nbsp
    </td>

    <td class="property-table-featured" data-label="<?php echo esc_html__('Featured', 'houzez'); ?>">
        <?php echo houzez_get_listing_data('featured') ? esc_html__('Yes', 'houzez') : esc_html__('No', 'houzez'); ?>
    </td>
    
    <td>
        <?php
        echo date_i18n(get_option('date_format'), strtotime($post->post_date));
        echo '<br>';
        echo date_i18n(get_option('time_format'), strtotime($post->post_date));
        echo '<br>';
        echo empty($post->post_author) 
            ? __('by a guest', 'houzez') 
            : sprintf(__('by %s', 'houzez'), '<a href="' . esc_url(add_query_arg('user', $post->post_author, $listings_page)) . '">' . get_the_author() . '</a>');
        ?>
    </td>

    <td class="property-table-actions" data-label="<?php echo esc_html__('Actions', 'houzez'); ?>">
        <div class="dropdown property-action-menu">
            <button class="btn btn-primary-outlined dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php echo esc_html__('Actions', 'houzez'); ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                <?php if (class_exists('Fave_Insights') && !empty($insights_page)) { ?>
                    <a class="dropdown-item" href="<?php echo esc_url($insights_page_link); ?>"><?php esc_html_e('View Stats', 'houzez'); ?></a>
                <?php } ?>

                <?php if ($property_status != 'expired') { ?>
                    <a class="dropdown-item" href="<?php echo esc_url($edit_link); ?>"><?php esc_html_e('Edit', 'houzez'); ?></a>
                <?php } ?>

                <!-- Add Editors Button -->
                <a class="dropdown-item" href="#">Add Editors</a>

                <a href="#" class="delete-property dropdown-item" data-id="<?php echo intval($post->ID); ?>" data-nonce="<?php echo wp_create_nonce('delete_my_property_nonce'); ?>"><?php esc_html_e('Delete', 'houzez'); ?></a>

                <a class="clone-property dropdown-item" data-nonce="<?php echo wp_create_nonce('clone_property_nonce'); ?>" data-property="<?php echo $post->ID; ?>" href="#"><?php esc_html_e('Duplicate', 'houzez'); ?></a>

                <?php if (houzez_is_published($post->ID)) { ?>
                    <a href="#" class="<?php echo esc_attr($put_on_hold_class); ?> dropdown-item" data-property="<?php echo intval($post->ID); ?>" data-nonce="<?php echo wp_create_nonce('puthold_property_nonce'); ?>"> 
                        <?php esc_html_e('Put On Hold', 'houzez'); ?>
                    </a>
                <?php } elseif (houzez_on_hold($post->ID)) { ?>
                    <a href="#" class="<?php echo esc_attr($put_on_hold_class); ?> dropdown-item" data-property="<?php echo intval($post->ID); ?>" data-nonce="<?php echo wp_create_nonce('puthold_property_nonce'); ?>"> 
                        <?php esc_html_e('Go Live', 'houzez'); ?>
                    </a>
                <?php } ?>

                <?php if (houzez_is_published($post->ID) && houzez_option('enable_mark_as_sold', 0)) { ?>
                    <a href="#" class="mark_as_sold_js dropdown-item" data-property="<?php echo intval($post->ID); ?>" data-nonce="<?php echo wp_create_nonce('sold_property_nonce'); ?>"> 
                        <?php esc_html_e('Mark as Sold', 'houzez'); ?>
                    </a>
                <?php } ?>

                <?php if ($is_user_can_manage) {
                    $post_id = intval($post->ID);

                    if (in_array($post->post_status, array('pending', 'disapproved'))) { 
                        echo "<a href='#' data-propid='{$post_id}' data-type='approve' class='dropdown-item houzez-prop-action-js'><strong>" . esc_html__('Approve', 'houzez') . '</strong></a>';
                    }

                    if (in_array($post->post_status, array('pending', 'publish'))) { 
                        echo "<a href='#' data-propid='{$post_id}' data-type='disapprove' class='dropdown-item houzez-prop-action-js'><strong>" . esc_html__('Disapproved', 'houzez') . '</strong></a>';
                    }

                    if (in_array($post->post_status, array('publish')) && !$fave_featured) { 
                        echo "<a href='#' data-propid='{$post_id}' data-type='set_featured' class='dropdown-item houzez-prop-action-js'><strong>" . esc_html__('Mark as Featured', 'houzez') . '</strong></a>';
                    }

                    if (in_array($post->post_status, array('publish')) && $fave_featured) { 
                        echo "<a href='#' data-propid='{$post_id}' data-type='remove_featured' class='dropdown-item houzez-prop-action-js'><strong>" . esc_html__('Remove from Featured', 'houzez') . '</strong></a>';
                    }

                    if (in_array($post->post_status, array('publish'))) { 
                        echo "<a href='#' data-propid='{$post_id}' data-type='expire' class='dropdown-item houzez-prop-action-js'>" . esc_html__('Mark as Expired', 'houzez') . '</a>';
                    }

                    if (in_array($post->post_status, array('expired', 'houzez_sold', 'draft'))) { 
                        echo "<a href='#' data-propid='{$post_id}' data-type='publish' class='dropdown-item houzez-prop-action-js'>" . esc_html__('Publish', 'houzez') . '</a>';
                    }
                } else {
                    if ($paid_submission_type == 'per_listing' && $property_status != 'expired') {
                        if ($payment_status != 'paid') {
                            if (houzez_is_woocommerce()) {
                                $eh_title = esc_html__('Pay Now', 'houzez');
                                echo "<a href='#' class='houzez-woocommerce-pay btn pay-btn' data-listid='{$post_id}'>{$eh_title}</a>";
                            } else {
                                $eh_title = esc_html__('Pay Now', 'houzez');
                                echo "<a href='{$payment_page_link}' class='btn pay-btn'>{$eh_title}</a>";
                            }
                        } else {
                            if (houzez_get_listing_data('featured') != 1 && $property_status == 'publish') {
                                if (houzez_is_woocommerce()) {
                                    $eh_title = esc_html__('Upgrade to Featured', 'houzez');
                                    echo "<a href='#' class='houzez-woocommerce-pay btn pay-btn' data-featured='1' data-listid='{$post_id}'>{$eh_title}</a>";
                                } else {
                                    $eh_title = esc_html__('Upgrade to Featured', 'houzez');
                                    echo "<a href='{$payment_page_link_featured}' class='btn pay-btn'>{$eh_title}</a>";
                                }
                            }
                        }
                    }

                    if ($property_status == 'expired' && ($paid_submission_type == 'per_listing')) {
                        if (houzez_is_woocommerce()) {
                            $eh_title = esc_html__('Re-List', 'houzez');
                            echo "<a href='#' data-listid='{$post_id}' class='houzez-woocommerce-pay btn pay-btn'>{$eh_title}</a>";
                        } else {
                            $payment_page_link_expired = add_query_arg(array('prop-id' => $post_id, 'mode' => 'relist'), $payment_page);
                            echo "<a href='{$payment_page_link_expired}' class='btn pay-btn'>{$eh_title}</a>";
                        }
                    }

                    if ($property_status == 'expired' && ($paid_submission_type == 'free_paid_listing' || $paid_submission_type == 'no')) {
                        $eh_title = esc_html__('Re-List', 'houzez');
                        echo "<a href='#' data-property='{$post_id}' class='relist-free btn pay-btn'>{$eh_title}</a>";
                    }

                    if (houzez_check_post_status($post->ID)) {
                        // Membership
                        if ($paid_submission_type == 'membership' && houzez_get_listing_data('featured') != 1 && $property_status == 'publish') {
                            $eh_title = esc_html__('Set as Featured', 'houzez');
                            echo "<a href='#' data-proptype='membership' data-propid='{$post_id}' class='make-prop-featured btn pay-btn'>{$eh_title}</a>";
                        }
                        if ($paid_submission_type == 'membership' && houzez_get_listing_data('featured') == 1) {
                            $eh_title = esc_html__('Remove From Featured', 'houzez');
                            echo "<a href='#' data-proptype='membership' data-propid='{$post_id}' class='remove-prop-featured btn pay-btn'>{$eh_title}</a>";
                        }
                        if ($property_status == 'expired' && $paid_submission_type == 'membership') {
                            $eh_title = esc_html__('Reactivate Listing', 'houzez');
                            echo "<a href='#' data-propid='{$post_id}' class='resend-for-approval btn pay-btn'>{$eh_title}</a>";
                        }

                        // Paid Featured
                        if ($paid_submission_type == 'free_paid_listing' && $property_status == 'publish') {
                            if (houzez_get_listing_data('featured') != 1) {
                                if (houzez_is_woocommerce()) {
                                    $eh_title = esc_html__('Upgrade to Featured', 'houzez');
                                    echo "<a href='#' class='houzez-woocommerce-pay btn pay-btn' data-featured='1' data-listid='{$post_id}'>{$eh_title}</a>";
                                } else {
                                    $eh_title = esc_html__('Upgrade to Featured', 'houzez');
                                    echo "<a href='{$payment_page_link_featured}' class='btn pay-btn'>{$eh_title}</a>";
                                }
                            }
                        }
                    }
                }
                ?>
            </div>
        </div>
    </td>
</tr>