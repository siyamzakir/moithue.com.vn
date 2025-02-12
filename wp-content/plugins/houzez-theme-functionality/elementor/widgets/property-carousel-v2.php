<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Properties Carousel v2 Widget.
 * @since 2.0
 */
class Houzez_Elementor_Properties_Carousels_v2n extends Widget_Base {
    use Houzez_Filters_Traits;
    use Houzez_Property_Cards_Traits;
    

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 2.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'houzez_elementor_properties_carousel_v2n';
    }

    /**
     * Get widget title.
     * @since 2.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Property Cards Carousel v2', 'houzez-theme-functionality' );
    }

    /**
     * Get widget icon.
     *
     * @since 2.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'houzez-element-icon eicon-posts-carousel';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 2.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return [ 'houzez-elements' ];
    }

    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 2.0
     * @access protected
     */
    protected function register_controls() {

        $this->start_controls_section(
            'filters_section',
            [
                'label'     => esc_html__( 'Carousel Settings', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'listing_thumb',
                'exclude' => [ 'custom', 'thumbnail', 'houzez-image_masonry', 'houzez-map-info', 'houzez-variable-gallery', 'houzez-gallery' ],
                'include' => [],
                'default' => 'houzez-item-image-1',
            ]
        );

        $this->Property_Cards_Carousel_Traits();
        
        $this->end_controls_section();

        $this->start_controls_section(
            'content_section',
            [
                'label'     => esc_html__( 'Filters', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->listings_cards_filters();

        $this->listings_cards_general_filters();

        $this->add_control(
            'all_btn',
            [
                'label'     => esc_html__("All - Button Text", "houzez-theme-functionality"),
                'type'      => Controls_Manager::TEXT,
                'description' => '',
            ]
        );
        $this->add_control(
            'all_url',
            [
                'label'     => esc_html__("All - Button URL", "houzez-theme-functionality"),
                'type'      => Controls_Manager::TEXT,
                'description' => '',
            ]
        );
        
        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Show/Hide 
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'hide_show_section',
            [
                'label'     => esc_html__( 'Show/Hide Data', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->Property_Cards_Show_Hide_Traits();

        $this->add_control(
            'hide_excerpt',
            [
                'label' => esc_html__( 'Hide Excerpt', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => 'none',
                'selectors' => [
                    '{{WRAPPER}} .property-carousel-module .item-short-description' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'hide_author_date',
            [
                'label' => esc_html__( 'Hide Date & Agent', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property-carousel-module .item-footer' => 'display: {{VALUE}};',
                    '{{WRAPPER}} .property-carousel-module .item-author' => 'display: {{VALUE}};',
                    '{{WRAPPER}} .property-carousel-module .item-date' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Typography
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'typography_section',
            [
                'label'     => esc_html__( 'Typography', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_property_title',
                'label'    => esc_html__( 'Property Title', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_prop_address',
                'label'    => esc_html__( 'Address', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} address.item-address',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_prop_excerpt',
                'label'    => esc_html__( 'Excerpt', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-short-description',
                'condition' => [
                    'hide_excerpt' => ''
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_meta_labels',
                'label'    => esc_html__( 'Meta Labels', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-wrap-v2 .item-amenities li',
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_meta_figure',
                'label'    => esc_html__( 'Meta Figure', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .hz-figure',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_price',
                'label'    => esc_html__( 'Price', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-price',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_subprice',
                'label'    => esc_html__( 'Sub Price', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-sub-price',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_types',
                'label'    => esc_html__( 'Property Type', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .h-type span',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_area-postfix',
                'label'    => esc_html__( 'Area Postfix', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .area_postfix',
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_agent',
                'label'    => esc_html__( 'Agent', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-author a',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'hz_item_date',
                'label'    => esc_html__( 'Date', 'houzez-theme-functionality' ),
                'selector' => '{{WRAPPER}} .item-date',
            ]
        );

        $this->end_controls_section();


        /*--------------------------------------------------------------------------------
        * Margin and Spacing
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'hz_spacing_margin_section',
            [
                'label'     => esc_html__( 'Spaces & Sizes', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'hz_title_margin_bottom',
            [
                'label' => esc_html__( 'Title Margin Bottom(px)', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .item-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'hz_address_margin_bottom',
            [
                'label' => esc_html__( 'Address Margin Bottom(px)', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .item-address' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'hz_excerpt_margin_bottom',
            [
                'label' => esc_html__( 'Excerpt Margin Bottom(px)', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .item-short-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'hide_Excerpt' => ''
                ]
            ]
        );

        $this->add_responsive_control(
            'hz_meta_icons',
            [
                'label' => esc_html__( 'Meta Icons Size(px)', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => '',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .item-amenities i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'hz_content_padding',
            [
                'label'      => esc_html__( 'Content Area Padding', 'houzez-theme-functionality' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .item-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Card Box
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'hz_grid_box_shadow',
            [
                'label' => esc_html__( 'Card Box', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->Property_Cards_Box_Traits();

        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Image 
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'hz_grid_images_styles',
            [
                'label' => esc_html__( 'Image Radius', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->Property_Cards_Image_Traits();

        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Colors
        * -------------------------------------------------------------------------------*/
        $this->start_controls_section(
            'hz_grid_colors',
            [
                'label' => esc_html__( 'Colors', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->Property_Cards_Colors_Traits();

        $this->add_control(
            'excerpt_color',
            [
                'label'     => esc_html__( 'Excerpt Color', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-short-description' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'hide_excerpt' => ''
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'author_color',
            [
                'label'     => esc_html__( 'Agent & Date', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .item-author a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .item-author' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .item-date' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        /*--------------------------------------------------------------------------------
        * Next Prev button
        * -------------------------------------------------------------------------------*/
        
        $this->Property_Cards_Carousel_Navi_Traits();

    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 2.0
     * @access protected
     */
    protected function render() {

        $settings = $this->get_settings_for_display();

        $property_type = $property_status = $property_label = $property_country = $property_state = $property_city = $property_area = $properties_by_agents = $properties_by_agencies = '';

        if(!empty($settings['property_type'])) {
            $property_type = implode (",", $settings['property_type']);
        }

        if(!empty($settings['property_status'])) {
            $property_status = implode (",", $settings['property_status']);
        }

        if(!empty($settings['property_label'])) {
            $property_label = implode (",", $settings['property_label']);
        }

        if(!empty($settings['property_country'])) {
            $property_country = implode (",", $settings['property_country']);
        }

        if(!empty($settings['property_state'])) {
            $property_state = implode (",", $settings['property_state']);
        }

        if(!empty($settings['property_city'])) {
            $property_city = implode (",", $settings['property_city']);
        }

        if(!empty($settings['property_area'])) {
            $property_area = implode (",", $settings['property_area']);
        }

        if( !empty($settings['properties_by_agents']) ) {
            $properties_by_agents = implode (",", $settings['properties_by_agents']);
        }

        if( !empty($settings['properties_by_agencies']) ) {
            $properties_by_agencies = implode (",", $settings['properties_by_agencies']);
        }
        
        $args['properties_by_agencies'] = $properties_by_agencies;

        $args['property_type']   =  $property_type;
        $args['property_status']   =  $property_status;
        $args['property_label']   =  $property_label;
        $args['property_country']   =  $property_country;
        $args['property_state']   =  $property_state;
        $args['property_city']   =  $property_city;
        $args['property_area']   =  $property_area;

        $args['featured_prop'] =  $settings['featured_prop'];
        $args['property_ids'] =  $settings['property_ids'];
        $args['posts_limit'] =  $settings['posts_limit'];
        $args['sort_by'] =  $settings['sort_by'];
        $args['offset'] =  $settings['offset'];
        $args['post_status'] =  $settings['post_status'];

        $args['all_btn'] = $settings['all_btn'];
        $args['all_url'] = $settings['all_url'];
        $args['slides_to_show'] = $settings['slides_to_show'];
        $args['slides_to_scroll'] = $settings['slides_to_scroll'];
        $args['slide_infinite'] = $settings['slide_infinite'];
        $args['slide_auto'] = $settings['slide_auto'];
        $args['auto_speed'] = $settings['auto_speed'];
        $args['navigation'] = $settings['navigation'];
        $args['slide_dots'] = $settings['slide_dots'];
        $args['thumb_size'] = $settings['listing_thumb_size'];
        $args['properties_by_agents'] = $properties_by_agents;
        $args['min_price'] = $settings['min_price'];
        $args['max_price'] = $settings['max_price'];

        $args['min_beds'] = $settings['min_beds'] ?? '';
        $args['max_beds'] = $settings['max_beds'] ?? '';
        $args['min_baths'] = $settings['min_baths'] ?? '';
        $args['max_baths'] = $settings['max_baths'] ?? '';

        
        if( function_exists( 'houzez_prop_carousel_v2' ) ) {
            echo houzez_prop_carousel_v2( $args );
        }

        if ( Plugin::$instance->editor->is_edit_mode() ) : 
            $token = wp_generate_password(5, false, false);
            ?>

            <script>
            jQuery('.houzez-properties-carousel-js[id^="houzez-properties-carousel-"]').each(function(){
                var $div = jQuery(this);
                var token = $div.data('token');
                var obj = window['houzez_prop_caoursel_' + token];

                var slides_to_scroll = <?php echo $settings['slides_to_scroll']; ?>,
                    slides_to_show = <?php echo $settings['slides_to_show']; ?>,
                    navigation = <?php echo $settings['navigation']; ?>,
                    auto_play = <?php echo $settings['slide_auto']; ?>,
                    auto_play_speed = parseInt(<?php echo $settings['auto_speed']; ?>),
                    dots = <?php echo $settings['slide_dots']; ?>,
                    slide_infinite =  <?php echo $settings['slide_infinite']; ?>;

                var houzez_rtl = houzez_vars.houzez_rtl;

                if( houzez_rtl == 'yes' ) {
                    houzez_rtl = true;
                } else {
                    houzez_rtl = false;
                }

                function parseBool(str) {
                    if( str == 'true' ) { return true; } else { return false; }
                }

                var houzezCarousel = jQuery('#houzez-properties-carousel-'+token);

                houzezCarousel.not('.slick-initialized').slick({
                    rtl: houzez_rtl,
                    lazyLoad: 'ondemand',
                    infinite: slide_infinite,
                    speed: 300,
                    slidesToShow: slides_to_show,
                    slidesToScroll: slides_to_scroll,
                    arrows: navigation,
                    autoplay: auto_play,
                    autoplaySpeed: auto_play_speed,
                    adaptiveHeight: true,
                    dots: dots,
                    appendArrows: '.houzez-carousel-arrows-'+token,
                    prevArrow: jQuery('.slick-prev-js-'+token),
                    nextArrow: jQuery('.slick-next-js-'+token),
                    responsive: [{
                            breakpoint: 992,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2
                            }
                        },
                        {
                            breakpoint: 769,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });

            });
            
            </script>
        
        <?php endif;

    }

}

Plugin::instance()->widgets_manager->register( new Houzez_Elementor_Properties_Carousels_v2n );