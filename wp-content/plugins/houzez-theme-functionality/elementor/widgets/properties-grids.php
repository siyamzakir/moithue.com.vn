<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Elementor Properties Grids Widget.
 * @since 1.5.6
 */
class Houzez_Elementor_Properties_Grids extends Widget_Base {
    use Houzez_Filters_Traits;
     

    /**
     * Get widget name.
     *
     * Retrieve widget name.
     *
     * @since 1.5.6
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'houzez_elementor_properties_grids';
    }

    /**
     * Get widget title.
     * @since 1.5.6
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Property Grids', 'houzez-theme-functionality' );
    }

    /**
     * Get widget icon.
     *
     * @since 1.5.6
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'houzez-element-icon eicon-posts-grid';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the widget belongs to.
     *
     * @since 1.5.6
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
     * @since 1.5.6
     * @access protected
     */
    protected function register_controls() {


        $this->start_controls_section(
            'content_section',
            [
                'label'     => esc_html__( 'Content', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'prop_grid_type',
            [
                'label'     => esc_html__( 'Grid Style', 'houzez-theme-functionality' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'grid_1'  => 'Grid v1',
                    'grid_2'    => 'Grid v2',
                    'grid_3'    => 'Grid v3',
                    'grid_4'    => 'Grid v4',
                ],
                'description' => '',
                'default' => 'grid_1',
            ]
        );

        $this->listings_cards_general_filters();

        
        $this->end_controls_section();

        //Filters
        $this->start_controls_section(
            'filters_section',
            [
                'label'     => esc_html__( 'Filters', 'houzez-theme-functionality' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->listings_cards_filters();

        $this->end_controls_section();

        $this->start_controls_section(
            'property_grids_settings',
            [
                'label' => esc_html__( 'Settings', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'hide_tools',
            [
                'label' => esc_html__( 'Hide Tools', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'none',
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .property-grids-module .item-tools' => 'display: {{VALUE}};',
                ],
            ]
        );


        $this->end_controls_section();

        

    }

    /**
     * Render widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.5.6
     * @access protected
     */
    protected function render() {
        global $ele_lazyloadbg; 
        
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

        if(!empty($settings['property_state'])) {
            $property_state = implode (",", $settings['property_state']);
        }

        if(!empty($settings['property_country'])) {
            $property_country = implode (",", $settings['property_country']);
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

        $args['prop_grid_type'] =  $settings['prop_grid_type'];
        $args['featured_prop'] =  $settings['featured_prop'];
        $args['posts_limit'] =  $settings['posts_limit'];
        $args['offset'] =  $settings['offset'];
        $args['post_status'] =  $settings['post_status'];

        $args['property_type']    =  $property_type;
        $args['property_status']  =  $property_status;
        $args['property_label']   =  $property_label;
        $args['property_country'] =  $property_country;
        $args['property_state']   =  $property_state;
        $args['property_city']    =  $property_city;
        $args['property_area']    =  $property_area;
        $args['properties_by_agents'] = $properties_by_agents;
        $args['properties_by_agencies'] = $properties_by_agencies;
        $args['min_price'] = $settings['min_price'];
        $args['max_price'] = $settings['max_price'];
        $args['property_ids'] =  $settings['property_ids'];

        $args['min_beds'] = $settings['min_beds'] ?? '';
        $args['max_beds'] = $settings['max_beds'] ?? '';
        $args['min_baths'] = $settings['min_baths'] ?? '';
        $args['max_baths'] = $settings['max_baths'] ?? '';

        $ele_lazyloadbg = '';
        if ( ! Plugin::$instance->editor->is_edit_mode() ) {
            $ele_lazyloadbg = houzez_get_lazyload_for_bg();
        }
        $args['ele_lazyloadbg']   =  $ele_lazyloadbg;
       
        if( function_exists( 'houzez_prop_grids' ) ) {
            echo houzez_prop_grids( $args );
        }

    }

}

Plugin::instance()->widgets_manager->register( new Houzez_Elementor_Properties_Grids );