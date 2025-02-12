<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Property_Content extends Widget_Base {
    use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-property-content';
	}

	public function get_title() {
		return __( 'Property Content', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon eicon-post-content';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder']; 
        }

        return [ 'houzez-single-property' ];
	}

	public function get_keywords() {
		return [ 'content', 'post', 'property', 'houzez' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
            'prop_content_typo',
            [
                'label' => __( 'Style', 'houzez-theme-functionality' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
            'prop_content_align',
            [
                'label' => __( 'Alignment', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => __( 'Justified', 'houzez-theme-functionality' ),
                        'icon' => 'eicon-text-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'prop_text_color',
            [
                'label' => __( 'Text Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography',
            ]
        );

        $this->end_controls_section();

	}

	

	protected function render() {

        $this->single_property_preview_query(); // Only for preview

		echo get_the_content();

        $this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Property_Content );