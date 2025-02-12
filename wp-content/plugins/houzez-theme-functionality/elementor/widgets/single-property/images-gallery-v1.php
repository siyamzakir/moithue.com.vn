<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Property_Images_Gallery_v1 extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-property-detail-gallery-v1';
	}

	public function get_title() {
		return __( 'Property Images Gallery v1', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon eicon-slider-push';
	}


    public function get_categories() {
        if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

        return [ 'houzez-single-property' ];
    }

	public function get_keywords() {
		return ['gallery', 'property gallery', 'houzez', 'top' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'gallery_type',
            [
                'label' => esc_html__( 'Popup Gallery Type', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
				'options' => [
					'builtin' => esc_html__( 'Built In', 'houzez-theme-functionality' ),
					'photoswipe' => esc_html__( 'Photo Swipe', 'houzez-theme-functionality' )
				],
				'default' => 'builtin',
            ]
        );

        $this->add_responsive_control(
            'gallery_height',
            [
                'label' => esc_html__( 'Height', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
                'selectors' => [
                    '{{WRAPPER}} .hs-gallery-v1-wrap .tab-content' => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'btn_gallery',
            [
                'label' => esc_html__( 'Gallery Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'btn_video',
            [
                'label' => esc_html__( 'Video Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'false',
            ]
        );

        $this->add_control(
            'btn_360_tour',
            [
                'label' => esc_html__( '360° Virtual Tour', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'false',
            ]
        );

        $this->add_control(
            'btn_map',
            [
                'label' => esc_html__( 'Map Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'map_note',
            [
                'label' => __( 'Map will only show if you have enabled when add/edit property', 'houzez-theme-functionality' ),
                'type' => 'houzez-warning-note',
                'condition' => [
                    'btn_map' => 'true'
                ]
            ]
        );

        $this->add_control(
            'btn_street',
            [
                'label' => esc_html__( 'Street View Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'street_note',
            [
                'label' => __( 'Street view will only show if you have enabled when add/edit property and map type set to Google', 'houzez-theme-functionality' ),
                'type' => 'houzez-warning-note',
                'condition' => [
                    'btn_street' => 'true'
                ]
            ]
        );


		$this->end_controls_section();
		
	}

	protected function render() {
		global $settings, $post;

		$settings = $this->get_settings_for_display();
        $this->single_property_preview_query(); // Only for preview

        $featured_image_url = houzez_get_image_url('full', $post->ID);
		$map_street_view = get_post_meta( $post->ID, 'fave_property_map_street_view', true );

		$listing_images = rwmb_meta( 'fave_property_images', 'type=plupload_image&size=full', $post->ID );
        $i = 0; $j = 0;
        $total_images = count($listing_images);

		$gallery_type = $settings['gallery_type'];
		$gallery_token = wp_generate_password(5, false, false);
		?>
		<div class="hs-gallery-v1-wrap hs-property-gallery-wrap">
            <div class="hs-gallery-v1-top-wrap property-top-wrap">
                <div class="property-banner">
                    <div class="container hidden-on-mobile">
                        <?php htf_get_template_part('elementor/template-part/single-property/media-btns'); ?>
                    </div><!-- container -->
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane show active" id="pills-gallery" role="tabpanel" aria-labelledby="pills-gallery-tab">
                            <?php
		                    if( $gallery_type == 'photoswipe' ) { 
                                $image_dimensions = houzez_get_image_dimensions_by_url($featured_image_url[0]);
                                $width = $image_dimensions['width'];
                                $height = $image_dimensions['height'];
                                ?>
                                <div id="houzez-photoswipe-gallery-<?php echo esc_attr($gallery_token); ?>" class="houzez-photoswipe" itemscope itemtype="http://schema.org/ImageGallery">
                                    <a class="property-banner-trigger" data-gallery-item href="<?php echo esc_url($featured_image_url[0]); ?>" data-pswp-width="<?php echo esc_attr($width); ?>" data-pswp-height="<?php echo esc_attr($height); ?>" itemprop="contentUrl">
                                    <img class="hs-gallery-image" src="<?php echo esc_url($featured_image_url[0]); ?>" width="<?php echo esc_attr($featured_image_url[1]); ?>" height="<?php echo esc_attr($featured_image_url[2]); ?>" alt="">
                                </a>
                                <?php
                                if(!empty($listing_images)) {
                                    $first_image = reset($listing_images);
                                    $skip_first = ($first_image['full_url'] == $featured_image_url);
                                    
                                    foreach( $listing_images as $prop_image_id => $prop_image_meta ) {
                                        // Skip if it's the first image and matches featured image
                                        if($skip_first && $prop_image_meta['full_url'] == $featured_image_url) {
                                            $skip_first = false; // Reset flag after skipping
                                            continue;
                                        }

                                        $image_dimensions = houzez_get_image_dimensions_by_url($prop_image_meta['full_url']);
                                        $width = $image_dimensions['width'];
                                        $height = $image_dimensions['height'];
                                        ?>
                                        <div itemprop="associatedMedia" itemscope itemtype="http://schema.org/ImageObject" class="gallery-hidden">
                                            <a href="<?php echo esc_url($prop_image_meta['full_url']); ?>" data-gallery-item itemprop="contentUrl" data-pswp-width="<?php echo esc_attr($width); ?>" data-pswp-height="<?php echo esc_attr($height); ?>">
                                                <img class="img-fluid" src="<?php echo esc_url($prop_image_meta['url']); ?>" alt="<?php echo esc_attr($prop_image_meta['alt']); ?>" itemprop="thumbnail">
                                            </a>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            
                            <?php

		                    } else { ?>
		                    <a href="#" class="property-banner-trigger" data-toggle="modal" data-target="#property-lightbox">
                                <img class="hs-gallery-image" src="<?php echo esc_url($featured_image_url[0]); ?>" width="<?php echo esc_attr($featured_image_url[1]); ?>" height="<?php echo esc_attr($featured_image_url[2]); ?>" alt="">
                            </a>
		                    <?php } ?>
                            <div class="property-image-count"><i class="houzez-icon icon-picture-sun"></i> <?php echo $total_images-3; ?></div>
                        </div><!-- tab-pane -->
                        
                        <?php 
		                if( houzez_get_listing_data('property_map') ) {
		                if( $settings['btn_map'] ) { ?>
		                <div class="tab-pane" id="pills-map" role="tabpanel" aria-labelledby="pills-map-tab">
		                    <?php get_template_part('property-details/partials/map'); ?>
		                </div>
		                <?php } ?>

		                <?php if(houzez_get_map_system() == 'google' && $map_street_view != 'hide' && $settings['btn_street'] ) { ?>
		                    <div class="tab-pane" id="pills-street-view" role="tabpanel" aria-labelledby="pills-street-view-tab">
		                    </div>
		                    <?php } ?>
		                <?php } ?>

		                <?php if( $settings['btn_video'] ) {
		                    $prop_video_url = houzez_get_listing_data('video_url');
		                    ?>
		                    <div class="tab-pane houzez-top-area-video" id="pills-video" role="tabpanel" aria-labelledby="pills-video-tab">
		                        <?php $embed_code = wp_oembed_get($prop_video_url); echo $embed_code; ?>
		                    </div>
		                <?php } ?>

		                <?php if( $settings['btn_360_tour'] ) { ?>
		                    <div class="tab-pane houzez-360-virtual-tour" id="pills-360tour" role="tabpanel" aria-labelledby="pills-360tour-tab">
		                        <?php echo houzez_get_listing_data('virtual_tour'); ?>
		                    </div>
		                <?php } ?>
                    </div><!-- tab-content -->
                </div><!-- property-banner -->
            </div><!-- property-top-wrap -->

            <div class="hs-gallery-v1-bottom-wrap">
                <div class="visible-on-mobile">
                    <div class="mobile-top-wrap">
                        <div class="mobile-property-tools clearfix">
                            <?php htf_get_template_part('elementor/template-part/single-property/media-btns'); ?>
                        </div><!-- mobile-property-tools -->
                    </div><!-- mobile-top-wrap -->
                </div><!-- visible-on-mobile -->
            </div><!-- hs-gallery-v1-bottom-wrap -->
        </div><!-- hs-gallery-v1-wrap -->
		<?php 
		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Property_Images_Gallery_v1 );