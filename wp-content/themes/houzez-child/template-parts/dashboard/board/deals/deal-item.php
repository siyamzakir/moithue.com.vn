<?php
global $deal_data;

$first_name = $last_name = $display_name = $lead_mobile = $lead_email = '';
$action_due_date = $deal_data->action_due_date; 
$action_due_date = str_replace('00:00:00', '', $action_due_date);
if($action_due_date == '0000-00-00 ') {
	$action_due_date = "";
}

$last_contact_date = $deal_data->last_contact_date; 
$last_contact_date = str_replace('00:00:00', '', $last_contact_date);
if($last_contact_date == '0000-00-00 ') {
	$last_contact_date = "";
}

$deal_id = $deal_data->deal_id;
$status = $deal_data->status;
$next_action = $deal_data->next_action;
$agent_id = $deal_data->agent_id;
$agent_name = get_the_title($agent_id);

$lead = Houzez_Leads::get_lead($deal_data->lead_id);

if(!empty($lead) ) {
	$display_name = $lead->display_name;
	$lead_mobile = $lead->mobile;
	$lead_email = $lead->email;

	if(empty($display_name)) {
		$display_name = $lead->first_name.' '.$lead->last_name;
	}
}

// Get the property details
$property = DB::findByColumns(DB::POSTS, ['ID' => $deal_data->listing_id], ['ID', 'post_title', 'post_name']);
?>
<tr data-id="<?php echo intval($deal_data->deal_id); ?>">
	<td class="table-nowrap" data-label="<?php esc_html_e('Title', 'houzez'); ?>">
		<strong><?php echo esc_attr($deal_data->title); ?></strong>
	</td>

	<!-- 
		- Developed By AppsZone
		- Add Ne Column Property
		-  
	-->

	<td class="table-nowrap" data-label="<?php esc_html_e('Property', 'houzez'); ?>">
		<?php 
			if($property){
				$post_title = isset($property['post_title']) ? $property['post_title'] : '';
				$post_name = isset($property['post_name']) ? $property['post_name'] : '';
				$property_link = rtrim(WP_HOME, '/') . "/property/" . ltrim($post_name, '/');

				// cut the post title to first 20 chars if it length is more than 20 and also add ...
				$title = strlen($post_title) > 20 ? substr($post_title, 0, 20) . '...' : $post_title;
				echo "<a href='{$property_link}' class='property-link'>{$title}</a>";
			} else {
				echo "<i>No Property Selected.</i>";
			}
		
		?>
	</td>
	<!-- End Additional Columns Like Property -->

	<td class="table-nowrap" data-label="<?php esc_html_e('Contact Name', 'houzez'); ?>">
		<?php echo  esc_attr($display_name); ?>
	</td>

	<?php if( houzez_is_admin() ) { ?>
	<td class="table-nowrap" data-label="<?php esc_html_e('Agent', 'houzez'); ?>">
		<?php if(!empty($agent_name)) { ?>
		<i class="houzez-icon icon-single-neutral-circle mr-2 grey"></i> <?php echo esc_attr($agent_name); ?>
		<?php } ?>
	</td>
	<?php } ?>

	<td class="table-nowrap" data-label="<?php esc_html_e('Status', 'houzez'); ?>">
		<select class="selectpicker deal_status form-control bs-select-hidden deals-status" title="<?php esc_html_e('Select', 'houzez'); ?>">
			<?php
			$status_settings = hcrm_get_option('status', 'hcrm_deals_settings', esc_html__('New Lead, Meeting Scheduled, Qualified, Proposal Sent, Called, Negotiation, Email Sent', 'houzez'));
			if(!empty($status_settings)) {

				$status_array = explode(',', $status_settings);
				foreach( $status_array as $status_name ) {
					echo '<option '.selected($status, trim($status_name), false).' value="'.trim($status_name).'">'.esc_attr($status_name).'</value>';
				}
			}
			?>
		</select>
	</td>
	<td class="table-nowrap" data-label="<?php esc_html_e('Next Action', 'houzez'); ?>">
		<select class="selectpicker deal_next_action form-control bs-select-hidden" title="<?php esc_html_e('Select', 'houzez'); ?>">
			<?php
			$next_action_settings = hcrm_get_option('next_action', 'hcrm_deals_settings', esc_html__('Qualification, Demo, Call, Send a Proposal, Send an Email, Follow Up, Meeting', 'houzez'));
			if(!empty($next_action_settings)) {

				$next_action_array = explode(',', $next_action_settings);
				foreach( $next_action_array as $action_name ) {
					echo '<option '.selected($next_action, trim($action_name), false).' value="'.trim($action_name).'">'.esc_attr($action_name).'</value>';
				}
			}
			?>
		</select>
	</td>
	<td class="table-nowrap" data-label="<?php esc_html_e('Action Due Date', 'houzez'); ?>">
		<input type="text" class="form-control deal_action_due" value="<?php echo esc_attr($action_due_date); ?>" placeholder="<?php esc_html_e('Select a Date', 'houzez'); ?>" readonly>
	</td>

	<td class="table-nowrap" data-label="<?php esc_html_e('Deal Value', 'houzez'); ?>">
		<?php echo esc_attr($deal_data->deal_value); ?>
	</td>

	<td class="table-nowrap" data-label="<?php esc_html_e('Last Contact Date', 'houzez'); ?>">
		<input type="text" class="form-control deal_last_contact" value="<?php echo esc_attr($last_contact_date); ?>" placeholder="<?php esc_html_e('Select a Date', 'houzez'); ?>" readonly>
	</td>

	<td class="table-nowrap" data-label="<?php esc_html_e('Phone', 'houzez'); ?>">
		<strong><?php echo esc_attr($lead_mobile); ?></strong>
	</td>

	<td class="table-nowrap" data-label="<?php esc_html_e('Email', 'houzez'); ?>">
		<a href="mailto:<?php echo esc_attr($lead_email); ?>"><strong><?php echo esc_attr($lead_email); ?></strong></a>
	</td>

	<?php if(houzez_is_admin() || DB::DEALS_LEADS_MANAGE_BY_SELF) { ?>
		<td class="table-nowrap">
			<div class="dropdown property-action-menu">
				<button class="btn btn-primary-outlined dropdown-toggle" 
					type="button" 
					id="dropdownMenuButton" 
					data-toggle="dropdown" 
					aria-haspopup="true"
					aria-expanded="false"
				>
					<?php esc_html_e('Actions', 'houzez'); ?>
				</button>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
					<a class="crm-edit-deal-js dropdown-item open-close-slide-panel" data-id="<?php echo intval($deal_id)?>" href="#">
						<?php esc_html_e('Edit', 'houzez'); ?>
					</a>
					<a class="delete-deal-js dropdown-item" 
						href="#"
						data-id="<?php echo intval($deal_id)?>" 
						data-nonce="<?php echo wp_create_nonce('delete_deal_nonce') ?>"
					>
							<?php esc_html_e('Delete', 'houzez'); ?>
					</a>
				</div>
			</div>
		</td>
	<?php } ?>
</tr>