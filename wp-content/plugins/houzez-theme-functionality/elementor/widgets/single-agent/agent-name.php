<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Agent_Name extends Widget_Base {
	use Houzez_Style_Traits;
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-agent-name';
	}

	public function get_title() {
		return __( 'Agent Name', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-agent eicon-post-title';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-agent')  {
            return ['houzez-single-agent-builder']; 
        }

		return [ 'houzez-single-agent' ];
	}

	public function get_keywords() {
		return [ 'houzez', 'agent name', 'title', 'heading', 'agent' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Agent Name', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'header_size',
			[
				'label' => esc_html__( 'HTML Tag', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Content', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->Houzez_Widget_Heading_Style();

		$this->end_controls_section();
		
	}

	protected function render() {
        $this->single_agent_preview_query(); // Only for preview

        $settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'title', 'class', 'elementor-heading-title' );

		if ( ! empty( $settings['size'] ) ) {
			$this->add_render_attribute( 'title', 'class', 'elementor-size-' . $settings['size'] );
		} else {
			$this->add_render_attribute( 'title', 'class', 'elementor-size-default' );
		}

		$permalink = get_permalink();

		$link = array('url' => $permalink);

		$title = get_the_title();

		if ( $settings['link'] && ! empty( $permalink ) ) {
			$this->add_link_attributes( 'url', $link );

			$title = sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'url' ), $title );
		}

		$title_html = sprintf( '<%1$s %2$s>%3$s</%1$s>', Utils::validate_html_tag( $settings['header_size'] ), $this->get_render_attribute_string( 'title' ), $title );

		// PHPCS - the variable $title_html holds safe data.
		echo $title_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		/*$this->add_render_attribute( 'title', 'class', [ 'agent_name' ] );
		echo '<div class="page-title">';
		parent::render();
		echo wp_kses_post( get_the_title() );
		echo '</div>';*/

		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Agent_Name );