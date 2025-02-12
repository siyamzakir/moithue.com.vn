<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Property_Images_Gallery_v3 extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-property-detail-gallery-v3';
	}

	public function get_title() {
		return __( 'Property Images Gallery v3', 'houzez-theme-functionality' );
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
		global $settings, $map_street_view, $post;

		$settings = $this->get_settings_for_display();
        $this->single_property_preview_query(); // Only for preview
		$map_street_view = get_post_meta( $post->ID, 'fave_property_map_street_view', true );

        $listing_images = rwmb_meta( 'fave_property_images', 'type=plupload_image&size=full', $post->ID );
        $total_images = count($listing_images);

		if ( Plugin::$instance->editor->is_edit_mode() ) : ?>

            <script>
                var houzez_rtl = houzez_vars.houzez_rtl;

                if( houzez_rtl == 'yes' ) {
                    houzez_rtl = true;
                } else {
                    houzez_rtl = false;
                }
                var listing_slider_variable_width = jQuery('.listing-slider-variable-width');
                if( listing_slider_variable_width.length > 0 ) {
                    listing_slider_variable_width.slick({
                        rtl: houzez_rtl,
                        infinite: true,
                        speed: 300,
                        slidesToShow: 1,
                        centerMode: true,
                        variableWidth: true,
                        arrows: true,
                        adaptiveHeight: false
                    });  

                    listing_slider_variable_width.slick('refresh');
                }

                function adjustTabContentHeight() {
                    var galleryHeight = jQuery('.hs-property-gallery-wrap .top-gallery-section').height();
                    jQuery('.tab-content').css('height', galleryHeight);
                }

                function checkAndAdjustLayout() {
                    adjustTabContentHeight();
                }

                // Recalculate the height on window load and resize
                jQuery(window).on('load resize', function() {
                    setTimeout(checkAndAdjustLayout, 100); // Ensure recalculation after a short delay
                });

                // Initial calculation
                checkAndAdjustLayout();
            </script>
        <?php endif; ?>

		<div class="hs-gallery-v3-wrap hs-property-gallery-wrap">
            <div class="hs-gallery-v3-top-wrap property-top-wrap">
                <div class="property-banner">
                    <div class="container hidden-on-mobile">
                        <?php htf_get_template_part('elementor/template-part/single-property/media-btns'); ?>
                    </div><!-- container -->
                    <div class="tab-content" id="pills-tabContent">
                        
                        <div class="tab-pane show active" id="pills-gallery" role="tabpanel" aria-labelledby="pills-gallery-tab">
                            <div class="property-image-count"><i class="houzez-icon icon-picture-sun"></i> <?php echo $total_images-3; ?></div>
                            <?php htf_get_template_part('elementor/template-part/single-property/partials/gallery-variable-width'); ?>
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

            <div class="hs-gallery-v3-bottom-wrap">
                <div class="visible-on-mobile">
                    <div class="mobile-top-wrap">
                        <div class="mobile-property-tools clearfix">
                            <?php htf_get_template_part('elementor/template-part/single-property/media-btns'); ?>
                        </div><!-- mobile-property-tools -->
                    </div><!-- mobile-top-wrap -->
                </div><!-- visible-on-mobile -->
            </div><!-- hs-gallery-v3-bottom-wrap -->
        </div><!-- hs-gallery-v3-wrap -->
		<?php 
		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Property_Images_Gallery_v3 );