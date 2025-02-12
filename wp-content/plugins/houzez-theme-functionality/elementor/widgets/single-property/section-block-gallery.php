<?php
namespace Elementor;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Property_Section_Block_Gallery extends \Elementor\Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use Houzez_Style_Traits;
    
	public function get_name() {
		return 'houzez-property-section-block-gallery';
	}

	public function get_title() {
		return __( 'Section Block Gallery', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon eicon-gallery-grid';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

        return [ 'houzez-single-property' ];
	}

	public function get_keywords() {
		return ['property', 'Block Gallery', 'houzez' ];
	}

	protected function register_controls() {
		parent::register_controls();


		$this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'houzez-theme-functionality' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'visible_images',
            [
                'label' => esc_html__( 'Visible Images', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => '9',
            ]
        );

        $this->add_responsive_control(
            'images_in_row',
            [
                'label' => esc_html__( 'Images in a row', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => array(
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                ),
            ]
        );

        $this->add_control(
            'popup_type',
            [
                'label' => esc_html__( 'Popup Type', 'houzez-theme-functionality' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'elementor',
                'options' => array(
                    'elementor' => 'Elementor',
                    'houzez' => 'Houzez',
                ),
            ]
        );

        $this->add_responsive_control(
            'grid_gap',
            [
                'label' => esc_html__( 'Gap', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 20,
                        'step' => .5,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .property-gallery-grid-wrap' => 'gap: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_responsive_control(
            'section_margin_top',
            [
                'label' => esc_html__( 'Margin Top', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .property-gallery-grid' => 'margin-top: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->end_controls_section();


	}

	protected function render() {
        global $post;
		$settings = $this->get_settings_for_display();
        $visible_images = $settings['visible_images'];

        // Get the value for desktop
        $images_in_row_desktop = $settings['images_in_row'];

        // Get the value for tablet
        $images_in_row_tablet = isset( $settings['images_in_row_tablet'] ) ? $settings['images_in_row_tablet'] : $images_in_row_desktop;

        // Get the value for mobile
        $images_in_row_mobile = isset( $settings['images_in_row_mobile'] ) ? $settings['images_in_row_mobile'] : $images_in_row_tablet;

        $this->single_property_preview_query(); // Only for preview

        if( empty($visible_images) ) {
            $visible_images = 9;
        }

        $gallery_token = wp_generate_password(5, false, false);

        

        $size = 'houzez-item-image-1';
        $properties_images = rwmb_meta( 'fave_property_images', 'type=plupload_image&size='.$size, $post->ID );

        $i = 0;

        if( !empty($properties_images) && count($properties_images)) {

            $total_images = count($properties_images);
            $remaining_images = $total_images - $visible_images;
        ?>
        <div class="property-gallery-grid property-section-wrap" id="property-gallery-grid">        
            <div class="property-gallery-grid-wrap houzez-desktop-layout-<?php echo esc_attr($images_in_row_desktop);?>cols houzez-tablet-layout-<?php echo esc_attr($images_in_row_tablet); ?>cols houzez-mobile-layout-<?php echo esc_attr($images_in_row_mobile); ?>cols">

                <?php 
                foreach( $properties_images as $image ) { $i++; 
                ?>
                <a <?php if($settings['popup_type'] == 'elementor') { ?> data-elementor-open-lightbox="yes" data-elementor-lightbox-slideshow="all-<?php echo esc_attr($gallery_token); ?>" href="<?php echo esc_url($image['full_url']);?>" <?php } else {?> data-toggle="modal" data-target="#property-lightbox" <?php } ?> class="gallery-grid-item <?php if($i == $visible_images){ echo 'more-images'; } elseif($i > $visible_images) {echo 'gallery-hidden'; } ?>">
                    <?php if( $i == $visible_images ){ echo '<span>'.esc_attr($remaining_images).'+</span>'; } ?>
                    <img class="img-fluid" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>">
                </a>
                <?php } ?>
                
            </div>
        </div><!-- property-gallery-grid -->
        <?php 
        }
        $this->reset_preview_query(); // Only for preview 
	}

}
\Elementor\Plugin::instance()->widgets_manager->register( new Property_Section_Block_Gallery() );