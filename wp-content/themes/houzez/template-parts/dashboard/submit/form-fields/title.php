<?php
global $property_data;
$property_title_limit = houzez_option('property_title_limit');
$enable_title_limit = houzez_option('enable_title_limit', 0);

$length = '';
$is_limit = false;
if ($enable_title_limit == 1 && $property_title_limit != '') {
	$is_limit = true;
	$length = 'maxlength="' . esc_attr($property_title_limit) . '"';
}

$property_title = houzez_edit_property() ? esc_attr($property_data->post_title) : '';
$property_title_placeholder = houzez_option('cl_prop_title_plac', 'Enter your property title');
?>
<div class="form-group">
	<label for="prop_title"><?php echo houzez_option('cl_prop_title', 'Property Title') . houzez_required_field('title'); ?></label>

	<?php if ($is_limit) { ?>
	<div class="title-counter"><span id="rchars">0</span><span> / <?php echo esc_attr($property_title_limit); ?></span></div>
	<?php } ?>
	

	<input class="form-control" 
		name="prop_title" id="prop_title" 
		value="<?= $property_title; ?>" 
		placeholder="<?= $property_title_placeholder; ?>" 
		<?php echo $length; ?> type="text"
		<?php houzez_required_field_2('title'); ?> 
	/>
</div>
<script>
	console.log(<?php echo json_encode(compact('property_title_limit', 'enable_title_limit', 'is_limit', 'length', 'property_data')); ?>);
</script>