<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Property_Images_Gallery_v5 extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-property-detail-gallery-v5';
	}

	public function get_title() {
		return __( 'Property Images Gallery v5', 'houzez-theme-functionality' );
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
                'exclude' => [ 'custom', 'thumbnail', 'houzez-image_masonry', 'houzez-map-info', 'houzez-variable-gallery', 'houzez-gallery' ],
                'include' => [],
                'default' => 'houzez-item-image-4',
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
		
        $size = $settings['image_size_size'] ?? 'houzez-item-image-4';
        $listing_images = rwmb_meta( 'fave_property_images', 'type=plupload_image&size='.$size, $post->ID );
        $i = 0; $j = 0;
        $total_images = count($listing_images);

        $featured_image_url = houzez_get_image_url('full', $post->ID);
        $map_street_view = get_post_meta( $post->ID, 'fave_property_map_street_view', true );

        $gallery_type = $settings['gallery_type'];
        $gallery_token = wp_generate_password(5, false, false);
        $css_class = 'houzez-trigger-popup-slider-js';
		?>

        <div class="hs-gallery-v5-wrap hs-property-gallery-wrap">
            <div class="hs-gallery-v5-top-wrap property-top-wrap">
                <?php
                if(!empty($listing_images)) { ?>
                <div class="property-banner">
                    <div class="hs-gallery-v5-grid-wrap">
                        <?php if($total_images > 5 ) { ?>
                        <div class="img-wrap-3-text">
                            <i class="houzez-icon icon-picture-sun mr-1"></i> 
                            <?php echo $total_images-5; ?> <?php echo esc_html__('More', 'houzez'); ?>
                        </div>
                        <?php } ?>
                        <div id="houzez-photoswipe-gallery-<?php echo esc_attr($gallery_token); ?>" class="hs-gallery-v5-grid houzez-photoswipe">
                            <?php
                            $g_data = 'href="#" data-toggle="modal" data-target="#property-lightbox"';

                            foreach( $listing_images as $image ) { $i++; 

                                $image_dimensions = houzez_get_image_dimensions_by_url($image['full_url']);
                                $width = $image_dimensions['width'];
                                $height = $image_dimensions['height'];

                                $g_data = 'href="#" data-toggle="modal" data-target="#property-lightbox" data-slider-no="'.esc_attr($i).'" data-image="'.esc_attr($j).'"';
                                
                                if( $gallery_type == 'photoswipe' ) {
                                    $g_data = 'data-gallery-item itemprop="contentUrl" data-pswp-width="'.esc_attr($width).'" data-pswp-height="'.esc_attr($height).'" href="'.esc_url( $image['full_url'] ).'"';
                                }
                            
                                if($i == 1) { ?>
                                <div class="hs-gallery-v5-grid-item hs-gallery-v5-grid-item-01">
                                    <a <?php echo $g_data; ?> data-slider-no="<?php echo esc_attr($i); ?>" data-image="<?php echo esc_attr($j); ?>" class="img-wrap-1 <?php echo esc_attr($css_class);?>">
                                        <img class="img-fluid" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                                    </a>
                                </div><!-- col-md-8 -->
                                <?php } elseif( $i == 2 || $i == 3  || $i == 4  || $i == 5 ) { ?>

                                <?php if($i == 2) { ?>
                                <div class="hs-gallery-v5-grid-item hs-gallery-v5-grid-item-02">
                                <?php } ?>
                                    <a <?php echo $g_data; ?> data-slider-no="<?php echo esc_attr($i); ?>" data-image="<?php echo esc_attr($j); ?>" class="img-wrap-<?php echo esc_attr($i); ?> <?php echo esc_attr($css_class);?>">
                                        
                                        <img class="img-fluid" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                                    </a>
                                <?php if( ($i == 5 && $total_images == 5) || ($i == 4 && $total_images == 4) || ($i == 3 && $total_images == 3) || ( $i == 2 && $total_images == 2 ) || ( $i == 1 && $total_images == 1 ) || $i == 5 ) { ?>
                                </div><!-- col-md-4 -->
                                <?php } ?>
                                <?php } else { ?>
                                    <a <?php echo $g_data; ?> class="img-wrap-1 gallery-hidden">
                                        <img class="img-fluid" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                                    </a>
                                <?php
                                }
                                $j++;
                            }
                            ?>
                        </div><!-- row -->
                    </div><!-- hidden-on-mobile -->
                </div><!-- property-banner -->
                <?php } ?>
            </div><!-- property-top-wrap -->
        </div><!-- hs-property-gallery-wrap -->
		<?php 
		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Property_Images_Gallery_v5 );