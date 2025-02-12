<?php 
global $settings;
$rating_id = get_the_ID();
$total_ratings = get_post_meta($rating_id, 'houzez_total_rating', true); 

$permalink = '';
if( ! is_singular() ) {
	$permalink = get_permalink();
}

if( empty( $total_ratings ) ) {
	$total_ratings = 0;
}
?>

<div class="rating-score-ele-wrap">
	<div class="d-flex">
	    <?php if( $settings['show_rating_count'] ) { ?>
	        <span class="rating-score-text"><?php echo esc_attr(round($total_ratings, 2)); ?></span>
	    <?php } ?>

	    <?php if( $settings['show_rating_stars'] ) { ?>
	    <div class="stars">
		    <?php echo houzez_get_stars($total_ratings, false); ?>
		</div>
	    <?php } ?>

	    <?php if( $settings['show_rating_text'] ) { ?>
	        <a class="all-reviews" href="<?php echo esc_url($permalink);?>#review-scroll"><?php echo esc_attr($settings['all_review_text']); ?></a>
	    <?php } ?>
	</div>
</div><!-- rating-score-wrap -->