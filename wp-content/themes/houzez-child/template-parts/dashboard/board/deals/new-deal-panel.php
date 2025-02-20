<?php
	global $deal_settings; 
	$agency_id = get_user_meta(get_current_user_id(), 'fave_author_agency_id', true); 
?>

<div class="dashboard-slide-panel-wrap deal-panel-wrap-js">
	<form id="deal-form">
		<h2><?php esc_html_e('Add New Deal', 'houzez'); ?></h2>
		<button type="button" class="btn open-close-slide-panel">
			<span aria-hidden="true">&times;</span>
		</button>
		<div class="form-group">
			<label><?php esc_html_e('Group', 'houzez'); ?></label>
			<select name="deal_group" class="selectpicker form-control bs-select-hidden" title="<?php esc_html_e('Select', 'houzez'); ?>" data-live-search="false">
				<option value="active"><?php esc_html_e('Active Deals', 'houzez'); ?></option>
				<option value="won"><?php esc_html_e('Won Deals', 'houzez'); ?></option>
				<option value="lost"><?php esc_html_e('Lost Deals', 'houzez'); ?></option>
			</select><!-- selectpicker -->
		</div><!-- form-group -->
		<div class="form-group">
			<label><?php esc_html_e('Title', 'houzez'); ?></label>
			<input class="form-control" name="deal_title" placeholder="<?php esc_html_e('Enter the deal title', 'houzez'); ?>" type="text">
		</div>

		<!--
			- Developed By AppsZone
			- Start Date: 19 Feb. 2025 - 12:46AM
		-->
		<div class="form-group">
			<label><?php esc_html_e('Contact Name', 'houzez'); ?></label>
			<select name="deal_contact" class="selectpicker form-control bs-select-hidden" 
				title="<?php esc_html_e('Select One Leads', 'houzez'); ?>" data-live-search="true"
			>
				<?php foreach (Houzez_leads::get_all_leads() as $lead): 
					if($lead->is_locked == 0): ;
				?>
					<option value="" hidden></option>
					<option value="<?= intval($lead->lead_id); ?>"><?= esc_html($lead->display_name); ?></option>
				<?php endif; endforeach; ?>
			</select>
		</div>
		<!-- End Developed By AppsZone-->

		<?php
		 	if(houzez_is_admin()) {
				echo Section::userSelectBox('deal_agent', 'Agent', 'Select Agent', ['select' => 'required']);
				echo Section::postSelectBox(name: 'listing_id', attributes: ['div'=> "id='deal-property-select'", 'select' => 'required']);
			} else {
				$user_id = get_current_user_id();

				// Accessing additional profile data
				$first_name = get_user_meta($user_id, 'first_name', true);
				$last_name = get_user_meta($user_id, 'last_name', true);
				$profile_picture = get_user_meta($user_id, 'profile_picture', true); // Example custom field

				$name = "{$first_name} {$last_name}";
				if(empty(trim($name))) {
					$name = wp_get_current_user()->display_name;
				}

				echo "
					<div class='form-group'>
						<label for='deal_agent'>Agent <sub class='text-success px-1'>(you)</sub></label>
						<input name='deal_agent' hidden type='text' value='{$user_id}' readonly>
						<input class='form-control' type='text' value='{$name}' readonly>
					</div>
				";
				
				// show post's/properties dropdown menus
				echo Section::postSelectBox(name: 'listing_id', attributes: ['div'=> "id='deal-property-select'", 'select' => 'required'], userId: $user_id);
			}
		?>

		<div class="form-group">
			<label><?php esc_html_e('Deal Value', 'houzez'); ?></label>
			<input class="form-control" name="deal_value" placeholder="<?php esc_html_e('Enter the deal value', 'houzez'); ?>" type="text">
		</div>
		<button id="add_deal" type="button" class="btn btn-primary btn-full-width mt-2">
			<?php get_template_part('template-parts/loader'); ?>
			<?php esc_html_e('Save', 'houzez'); ?>		
		</button>

		<?php get_template_part('template-parts/overlay-loader'); ?>
		
		<input type="hidden" name="action" value="houzez_crm_add_deal">
		<br/>
		<div id="deal-msgs"></div>
	</form>
</div><!-- dashboard-slide-panel-wrap -->