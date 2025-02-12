<?php
global $settings, $post;
$size = 'houzez-variable-gallery';
$properties_images = rwmb_meta( 'fave_property_images', 'type=plupload_image&size='.$size, $post->ID );
$gallery_caption = houzez_option('gallery_caption', 0); 
$property_gallery_popup_type = houzez_get_popup_gallery_type(); 
$featured_image_url = houzez_get_image_url('full', $post->ID);

if( isset($settings['gallery_type']) ) {
	$property_gallery_popup_type = $settings['gallery_type'];
}
$gallery_token = wp_generate_password(5, false, false);

if( !empty($properties_images) && count($properties_images)) {
?>
<div class="top-gallery-section top-gallery-variable-width-section">
	
	<?php if( $property_gallery_popup_type == 'photoswipe' ) { ?>
		<div id="houzez-photoswipe-gallery-<?php echo esc_attr($gallery_token); ?>" class="listing-slider-variable-width houzez-all-slider-wrap houzez-photoswipe" itemscope itemtype="http://schema.org/ImageGallery">
			<?php
			$first_image = reset($properties_images);
			$skip_first = ($first_image['full_url'] == $featured_image_url[0]);
			
			foreach( $properties_images as $prop_image_id => $prop_image_meta ) {
				// Skip if it's the first image and matches featured image
				if($skip_first && $prop_image_meta['full_url'] == $featured_image_url[0]) {
					$skip_first = false;
					continue;
				}

				$image_dimensions = houzez_get_image_dimensions_by_url($prop_image_meta['full_url']);
				$width = $image_dimensions['width'];
				$height = $image_dimensions['height'];
				?>
				<div itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject">
					<a href="<?php echo esc_url($prop_image_meta['full_url']); ?>" data-gallery-item itemprop="contentUrl" data-pswp-width="<?php echo esc_attr($width); ?>" data-pswp-height="<?php echo esc_attr($height); ?>">
						<img class="img-responsive" data-lazy="<?php echo esc_attr($prop_image_meta['url']); ?>" src="<?php echo esc_attr($prop_image_meta['url']); ?>" alt="<?php echo esc_attr($prop_image_meta['alt']); ?>" title="<?php echo esc_attr($prop_image_meta['title']); ?>" itemprop="thumbnail">
						<?php if( !empty($prop_image_meta['caption']) && $gallery_caption != 0 ) { ?>
							<span class="hz-image-caption"><?php echo esc_attr($prop_image_meta['caption']); ?></span>
						<?php } ?>
					</a>
				</div>
				<?php
			}
			?>
		</div>
	<?php } else { ?>
		<div class="listing-slider-variable-width houzez-all-slider-wrap">
			<?php
			$i = 0;
			foreach( $properties_images as $prop_image_id => $prop_image_meta ) { $i++; ?>
				<div>
					<a rel="gallery-1" href="#" data-slider-no="<?php echo esc_attr($i); ?>" class="houzez-trigger-popup-slider-js swipebox" data-toggle="modal" data-target="#property-lightbox">
						<img class="img-responsive" data-lazy="<?php echo esc_attr($prop_image_meta['url']); ?>" src="<?php echo esc_attr($prop_image_meta['url']); ?>" alt="<?php echo esc_attr($prop_image_meta['alt']); ?>" title="<?php echo esc_attr($prop_image_meta['title']); ?>">
						<?php if( !empty($prop_image_meta['caption']) && $gallery_caption != 0 ) { ?>
							<span class="hz-image-caption"><?php echo esc_attr($prop_image_meta['caption']); ?></span>
						<?php } ?>
					</a>
				</div>
			<?php }
			?>
		</div>
	<?php } ?>
	
</div><!-- top-gallery-section -->

<?php } ?>