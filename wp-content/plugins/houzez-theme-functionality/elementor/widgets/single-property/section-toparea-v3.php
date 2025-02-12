<?php
namespace Elementor;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Property_Toparea_v3 extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;
    use Houzez_Style_Traits;

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
        return 'houzez-property-toparea-v3';
    }

    public function get_title() {
        return __( 'Section Top Area v3 and v4', 'houzez-theme-functionality' );
    }

    public function get_icon() {
        return 'houzez-element-icon eicon-featured-image';
    }

    public function get_categories() {
        if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

        return [ 'houzez-single-property' ];
    }

    public function get_keywords() {
        return ['property', 'toparea 3, 4', 'houzez', 'gallery' ];
    }

    protected function register_controls() {
        parent::register_controls();


        $repeater = new Repeater();
        $field_types = array();

        $field_types = [
            'address' => esc_html__( 'Address', 'houzez-theme-functionality' ),
            'streat-address' => esc_html__( 'Streat Address', 'houzez-theme-functionality' ),
            'country' => esc_html__( 'Country', 'houzez-theme-functionality' ),
            'state' => esc_html__( 'State', 'houzez-theme-functionality' ),
            'city' => esc_html__( 'City', 'houzez-theme-functionality' ),
            'area' => esc_html__( 'area', 'houzez-theme-functionality' ),
            
        ];
        /**
         * field types.
         */
        $field_types = apply_filters( 'houzez/address_title', $field_types );

        $repeater->add_control(
            'field_type',
            [
                'label' => esc_html__( 'Field', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => $field_types,
                'default' => 'text',
            ]
        );

        $this->start_controls_section(
            'section_image_size',
            [
                'label' => __( 'Image', 'houzez-theme-functionality' ),
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

        $this->end_controls_section();
    
        //Breadcrumb
        $this->start_controls_section(
            'section_breadcrumb',
            [
                'label' => __( 'Breadcrumb', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_breadcrumb',
            [
                'label' => esc_html__( 'Show Breadcrumb', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->end_controls_section();

        $this->houzez_property_topareas_style_traits();

        // Property Title
        $this->start_controls_section(
            'section_prop_title',
            [
                'label' => __( 'Property Title', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => esc_html__( 'Show Title', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'prop_title_color',
            [
                'label' => __( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .page-title h1' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'prop_title_typography',
                'selector' => '{{WRAPPER}} .page-title h1',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'prop_title_text_shadow',
                'label' => __( 'Text Shadow', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .page-title h1',
            ]
        );

        $this->add_responsive_control(
            'title_info_margin_top',
            [
                'label' => __( 'Margin Top', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .page-title-wrap .page-title, .mobile-property-title .page-title' => 'margin-top: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_info_margin_bottom',
            [
                'label' => __( 'Margin Bottom', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .page-title-wrap .page-title, .mobile-property-title .page-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        
        $this->end_controls_section();

        // Property labels
        $this->start_controls_section(
            'section_prop_labels',
            [
                'label' => __( 'Property Labels', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_labels',
            [
                'label' => esc_html__( 'Show Labels', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->end_controls_section();

        // Property Address
        $this->start_controls_section(
            'section_prop_address',
            [
                'label' => __( 'Property Address', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_address',
            [
                'label' => esc_html__( 'Show Address', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'address_fields',
            [
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        '_id' => 'address',
                        'field_type' => 'address',
                    ],
                ],
                'title_field' => '{{{ field_type }}}',
            ]
        );

        $this->add_control(
            'hide_icon',
            [
                'label' => esc_html__( 'Hide Icon', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .item-address .icon-pin' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'address_color',
            [
                'label' => __( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .item-address' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'address_typography',
                'selector' => '{{WRAPPER}} .item-address',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'address_text_shadow',
                'label' => __( 'Text Shadow', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-address',
            ]
        );

        $this->end_controls_section();


        // Property Price
        $this->start_controls_section(
            'section_prop_price',
            [
                'label' => __( 'Property Price', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_price',
            [
                'label' => esc_html__( 'Show Price', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'item_price_color',
            [
                'label' => __( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .item-price' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_price_top',
            [
                'label' => __( 'Margin Top', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .item-price' => 'margin-top: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_price_bottom',
            [
                'label' => __( 'Margin Bottom', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', 'em' ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .item-price' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .item-price',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'item_price_text_shadow',
                'label' => __( 'Text Shadow', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-price',
            ]
        );

        $this->add_control(
            'item_sub_price_heading',
            [
                'label' => __( 'Second Price', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'item_sub_price_color',
            [
                'label' => __( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .item-sub-price' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'item_sub_price_typography',
                'selector' => '{{WRAPPER}} .item-sub-price',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'item_sub_price_text_shadow',
                'label' => __( 'Text Shadow', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-sub-price',
            ]
        );

        $this->end_controls_section();

        // Tools
        $this->start_controls_section(
            'section_Tools',
            [
                'label' => __( 'Tools', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'hide_favorite',
            [
                'label' => esc_html__( 'Favorite', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'hide_social',
            [
                'label' => esc_html__( 'Social Share', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'hide_print',
            [
                'label' => esc_html__( 'Print', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
            ]
        );

        $this->add_control(
            'buttons_bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_bg_color_hover',
            [
                'label'     => esc_html__( 'Background Color Hover', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_color',
            [
                'label'     => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_color_hover',
            [
                'label'     => esc_html__( 'Color Hover', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_border_color',
            [
                'label'     => esc_html__( 'Border Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'buttons_border_color_hover',
            [
                'label'     => esc_html__( 'Border Color Hover', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-tool > span:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        // Media Buttons
        $this->start_controls_section(
            'section_media',
            [
                'label' => __( 'Media Buttons', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
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
                'default' => 'true',
            ]
        );

        $this->add_control(
            'btn_360_tour',
            [
                'label' => esc_html__( '360° Virtual Tour Button', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'true',
                'default' => 'true',
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

        $size = $settings['image_size_size'] ?? 'houzez-gallery';
        $settings = $this->get_settings_for_display();

        $this->single_property_preview_query(); // Only for preview

        $featured_image_url = houzez_get_image_url('full', $post->ID);
        $map_street_view = get_post_meta($post->ID, 'fave_property_map_street_view', true);

        $properties_images = rwmb_meta('fave_property_images', 'type=plupload_image&size='.$size, $post->ID);
        $gallery_caption = houzez_option('gallery_caption', 0);
        $gallery_type = houzez_get_popup_gallery_type();
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
        <?php
        }
        ?>
        <div class="property-wrap property-detail-v4">
            <?php if (
                $settings['show_breadcrumb'] ||
                $settings['show_title'] ||
                $settings['show_price'] ||
                $settings['show_address'] ||
                $settings['show_labels']
            ) {
                htf_get_template_part('elementor/template-part/single-property/property-title');
            } ?>
        
            <div class="property-top-wrap">
                <div class="property-banner">
                    <div class="container hidden-on-mobile">
                        <?php htf_get_template_part('elementor/template-part/single-property/media-btns'); ?>
                    </div>
                    <div class="tab-content" id="pills-tabContent">
                        
                        <div class="tab-pane show active" id="pills-gallery" role="tabpanel" aria-labelledby="pills-gallery-tab">
                            <style type="text/css">
                                .listing-slider-ele {
                                    max-height: 751px !important;
                                }
                            </style>

                            <?php if (!empty($properties_images)) { ?>
                                <div class="top-gallery-section">
                                    <div id="property-gallery-js-<?php echo $this->get_id(); ?>" class="listing-slider listing-slider-ele houzez-photoswipe cS-hidden">
                                        <?php foreach ($properties_images as $prop_image_id => $prop_image_meta) {
                                            $thumb = houzez_get_image_by_id($prop_image_id, 'houzez-item-image-1');
                                            $image_dimensions = houzez_get_image_dimensions_by_url($prop_image_meta['full_url']);
                                            $width = $image_dimensions['width'];
                                            $height = $image_dimensions['height'];
                                            ?>
                                            <div data-thumb="<?php echo esc_url($thumb[0]); ?>">
                                                <?php if ($gallery_type == 'photoswipe') { ?>
                                                    <a data-gallery-item data-pswp-width="<?php echo esc_attr($width); ?>" data-pswp-height="<?php echo esc_attr($height); ?>" href="<?php echo esc_url($prop_image_meta['full_url']); ?>">
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
                        </div>

                        <?php if (houzez_get_listing_data('property_map') && $settings['btn_map']) { ?>
                            <div class="tab-pane" id="pills-map" role="tabpanel" aria-labelledby="pills-map-tab">
                                <?php get_template_part('property-details/partials/map'); ?>
                            </div>
                        <?php } ?>

                        <?php if (houzez_get_map_system() == 'google' && $map_street_view != 'hide' && $settings['btn_street']) { ?>
                            <div class="tab-pane" id="pills-street-view" role="tabpanel" aria-labelledby="pills-street-view-tab">
                            </div>
                        <?php } ?>

                        <?php if ($settings['btn_video']) {
                            $prop_video_url = houzez_get_listing_data('video_url');
                            ?>
                            <div class="tab-pane houzez-top-area-video" id="pills-video" role="tabpanel" aria-labelledby="pills-video-tab">
                                <?php echo wp_oembed_get($prop_video_url); ?>
                            </div>
                        <?php } ?>

                        <?php if ($settings['btn_360_tour']) { ?>
                            <div class="tab-pane houzez-360-virtual-tour" id="pills-360tour" role="tabpanel" aria-labelledby="pills-360tour-tab">
                                <?php echo houzez_get_listing_data('virtual_tour'); ?>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
        
        <?php 
        htf_get_template_part('elementor/template-part/single-property/mobile', 'view');
        
        $this->reset_preview_query(); // Only for preview
    }

}
Plugin::instance()->widgets_manager->register( new Property_Toparea_v3 );