<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Property_Images_Gallery_v2 extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function __construct( array $data = [], ?array $args = null ) {
        parent::__construct( $data, $args );

        $js_path = 'assets/frontend/js/';
        
        
        wp_register_script('lightslider', HOUZEZ_PLUGIN_URL . $js_path . 'lightslider.min.js', array('jquery'), '1.1.3', false);


        wp_register_script( 'houzez-elementor-single-gallery-top', HOUZEZ_PLUGIN_URL . $js_path . 'property-single-gallery.js', array( 'jquery', 'lightslider' ), '1.0.0' );

    }

    public function get_script_depends() {
        return [ 'lightslider', 'houzez-elementor-single-gallery-top' ];
    }

	public function get_name() {
		return 'houzez-property-detail-gallery-v2';
	}

	public function get_title() {
		return __( 'Property Images Gallery v2', 'houzez-theme-functionality' );
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

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'image_size',
                'exclude' => [ 'custom', 'thumbnail', 'houzez-top-v7','houzez-image_masonry', 'houzez-item-image-6', 'houzez-map-info', 'houzez-variable-gallery', 'houzez-item-image-4', 'houzez-item-image-1' ],
                'include' => [],
                'default' => 'houzez-gallery',
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
		global $settings, $post;

		$settings = $this->get_settings_for_display();
        $this->single_property_preview_query(); // Only for preview
        
        $image_size = $settings['image_size_size'] ?? 'houzez-gallery';

        $featured_image_url = houzez_get_image_url('full', $post->ID);
		$map_street_view = get_post_meta( $post->ID, 'fave_property_map_street_view', true );
		$listing_images = rwmb_meta('fave_property_images', 'type=plupload_image&size='.$image_size, $post->ID);

		$gallery_caption = houzez_option('gallery_caption', 0);
		$gallery_type = $settings['gallery_type'];
		$gallery_token = wp_generate_password(5, false, false);

		if ( Plugin::$instance->editor->is_edit_mode() ) { ?>

            <script type="application/javascript">
                houzezSingleTopGalleryElementor("<?php echo 'property-gallery-js-' . esc_attr($this->get_id()); ?>" );
            </script>
        <?php
        } else { ?>

            <script type="application/javascript">
                jQuery(document).bind("ready", function () {
                    houzezSingleTopGalleryElementor("<?php echo 'property-gallery-js-' . esc_attr($this->get_id()); ?>" );
                });
            </script>
        <?php } ?>
		<div class="hs-gallery-v2-wrap hs-property-gallery-wrap">
            <div class="hs-gallery-v2-top-wrap property-top-wrap">
                <div class="property-banner">
                    <div class="container hidden-on-mobile">
                        <?php htf_get_template_part('elementor/template-part/single-property/media-btns'); ?>
                    </div><!-- container -->
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane show active" id="pills-gallery" role="tabpanel" aria-labelledby="pills-gallery-tab">
                            <?php if (!empty($listing_images)) { ?>
                                <div class="top-gallery-section">
                                    <div id="property-gallery-js-<?php echo $this->get_id(); ?>" class="listing-slider listing-slider-ele houzez-photoswipe cS-hidden">
                                        <?php foreach ($listing_images as $prop_image_id => $prop_image_meta) {
                                            $thumb = houzez_get_image_by_id($prop_image_id, 'houzez-item-image-1');
                                            $image_dimensions = houzez_get_image_dimensions_by_url($prop_image_meta['full_url']);
                                            $width = $image_dimensions['width'];
                                            $height = $image_dimensions['height'];
                                            ?>
                                            <div data-thumb="<?php echo esc_url($thumb[0]); ?>">
                                                <?php if ($gallery_type == 'photoswipe') { ?>
                                                    <a data-gallery-item data-pswp-width="<?php echo esc_attr($width); ?>" data-pswp-height="<?php echo esc_attr($height); ?>"  href="<?php echo esc_url($prop_image_meta['full_url']); ?>">
                                                        <img class="img-fluid" src="<?php echo esc_url($prop_image_meta['url']); ?>" alt="<?php echo esc_attr($prop_image_meta['alt']); ?>" title="<?php echo esc_attr($prop_image_meta['title']); ?>">
                                                    </a>
                                                <?php } else { ?>
                                                    <a rel="gallery-1" href="#" class="swipebox" data-toggle="modal" data-target="#property-lightbox">
                                                        <img class="img-fluid" src="<?php echo esc_url($prop_image_meta['url']); ?>" alt="<?php echo esc_attr($prop_image_meta['alt']); ?>" title="<?php echo esc_attr($prop_image_meta['title']); ?>">
                                                    </a>
                                                <?php } ?>

                                                <?php if (!empty($prop_image_meta['caption']) && $gallery_caption != 0) { ?>
                                                    <span class="hz-image-caption"><?php echo esc_attr($prop_image_meta['caption']); ?></span>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
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

            <div class="hs-gallery-v2-bottom-wrap">
                <div class="visible-on-mobile">
                    <div class="mobile-top-wrap">
                        <div class="mobile-property-tools clearfix">
                            <?php htf_get_template_part('elementor/template-part/single-property/media-btns'); ?>
                        </div><!-- mobile-property-tools -->
                    </div><!-- mobile-top-wrap -->
                </div><!-- visible-on-mobile -->
            </div><!-- hs-gallery-v2-bottom-wrap -->
        </div><!-- hs-gallery-v2-wrap -->
		<?php 
		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Property_Images_Gallery_v2 );