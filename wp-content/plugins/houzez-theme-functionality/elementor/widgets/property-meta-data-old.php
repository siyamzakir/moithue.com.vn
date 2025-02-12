<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Houzez_Property_Meta_Data extends Widget_Base {
	use \HouzezThemeFunctionality\Elementor\Traits\Houzez_Preview_Query;

	public function get_name() {
		return 'houzez-property-meta';
	}

	public function get_title() {
		return __( 'Property Meta Data', 'houzez-theme-functionality' );
	}

	public function get_icon() {
		return 'houzez-element-icon houzez-listing eicon-post-excerpt';
	}

	public function get_categories() {
		if(get_post_type() === 'fts_builder' && htb_get_template_type(get_the_id()) === 'single-listing')  {
            return ['houzez-single-property-builder', 'houzez-elements']; 
        }

		return [ 'houzez-elements', 'houzez-single-property' ];
	}

	public function get_keywords() {
		return [ 'houzez', 'listing meta', 'property meta', 'property' ];
	}

	protected function register_controls() {
		parent::register_controls();

		$meta_fields = array();

        $meta_fields = [
            'property_type' => esc_html__( 'Property Type', 'houzez-theme-functionality' ),
            'property_rooms' => esc_html__( 'Rooms', 'houzez-theme-functionality' ),
            'property_bedrooms' => esc_html__( 'Bedrooms', 'houzez-theme-functionality' ),
            'property_bathrooms' => esc_html__( 'Bathrooms', 'houzez-theme-functionality' ),
            'property_size' => esc_html__( 'Area Size', 'houzez-theme-functionality' ),
            'property_land' => esc_html__( 'Land Area', 'houzez-theme-functionality' ),
            'property_garage' => esc_html__( 'Garages', 'houzez-theme-functionality' ),
            'property_year' => esc_html__( 'Built Year', 'houzez-theme-functionality' ),
            'property_status' => esc_html__( 'Property Status', 'houzez-theme-functionality' ),
            'property_id' => esc_html__( 'Property ID', 'houzez-theme-functionality' ),
            
        ];

        $meta_fields = array_merge($meta_fields, houzez_get_custom_field_for_elementor());

        /**
         * field types.
         */
        $meta_fields = apply_filters( 'houzez/property_meta_data', $meta_fields );

		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Property Meta', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
            'meta_layout',
            [
                'label' => esc_html__( 'Meta Layout', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'module-v1' => esc_html__( 'Layout v1', 'houzez-theme-functionality' ),
                    'module-v2' => esc_html__( 'Layout v2', 'houzez-theme-functionality' ),
                    'module-v3' => esc_html__( 'Layout v3', 'houzez-theme-functionality' ),
                    'module-v4' => esc_html__( 'Layout v4', 'houzez-theme-functionality' ),
                ],
                'default' => 'module-v1',
            ]
        );

        $this->add_control(
            'property_meta_field',
            [
                'label' => esc_html__( 'Select Meta', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => $meta_fields,
				'default' => '',
            ]
        );

        $this->add_control(
            'meta_label',
            [
                'label' => esc_html__( 'Meta Label', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
				'default' => '',
            ]
        );

        $this->add_control(
            'icon_type',
            [
                'label' => esc_html__( 'Meta Icon', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'default' => esc_html__( 'Default as Theme Options', 'houzez-theme-functionality' ),
                    'custom' => esc_html__( 'Custom Icon', 'houzez-theme-functionality' ),
                    'none' => esc_html__( 'No Icon', 'houzez-theme-functionality' ),
                ],
                'default' => 'default',
            ]
        );

        $this->add_control(
            'icon',
            [
                'label' => esc_html__( 'Icon', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::ICONS,
                'skin' => 'inline',
                'label_block' => false,
                'condition' => [
                    'icon_type' => 'custom'
                ]
            ]
        );

        /*$this->add_control(
			'html_tag',
			[
				'label' => esc_html__( 'HTML Tag', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'div',
				'conditions' => [
                    'terms' => [
                        [
                            'name' => 'agent_meta_field',
                            'operator' => '!in',
                            'value' => [
                                'fave_agent_whatsapp',
                                'fave_agent_line_id',
                                'fave_agent_telegram',
                                'fave_agent_skype',
                                'fave_agent_zillow',
                                'fave_agent_realtor_com',
                                'fave_agent_facebook',
                                'fave_agent_twitter',
                                'fave_agent_linkedin',
                                'fave_agent_googleplus',
                                'fave_agent_tiktok',
                                'fave_agent_instagram',
                                'fave_agent_pinterest',
                                'fave_agent_youtube',
                                'fave_agent_vimeo',
                            ],
                        ],
                    ],
                ],
			]
		);

		$this->add_control(
            'custom_icon',
            [
                'label' => esc_html__( 'Custom Icon', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Yes', 'houzez-theme-functionality' ),
                'label_off' => esc_html__( 'No', 'houzez-theme-functionality' ),
                'return_value' => 'yes',
				'default' => '',
				'conditions' => [
                    'terms' => [
                        [
                            'name' => 'agent_meta_field',
                            'operator' => 'in',
                            'value' => [
                                'fave_agent_whatsapp',
                                'fave_agent_line_id',
                                'fave_agent_telegram',
                                'fave_agent_skype',
                                'fave_agent_zillow',
                                'fave_agent_realtor_com',
                                'fave_agent_facebook',
                                'fave_agent_twitter',
                                'fave_agent_linkedin',
                                'fave_agent_googleplus',
                                'fave_agent_tiktok',
                                'fave_agent_instagram',
                                'fave_agent_pinterest',
                                'fave_agent_youtube',
                                'fave_agent_vimeo',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
			'meta_icon',
			[
				'label' => esc_html__( 'upload Icon', 'houzez-theme-functionality' ),
				'type' => Controls_Manager::ICONS,
				'condition' => [
                	'custom_icon' => 'yes'
                ],
			]
		);*/

		$this->end_controls_section();

		/*$this->start_controls_section(
			'section_agent_meta_style',
			[
				'label' => esc_html__( 'Text', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions' => [
                    'terms' => [
                        [
                            'name' => 'agent_meta_field',
                            'operator' => '!in',
                            'value' => [
                                'fave_agent_whatsapp',
                                'fave_agent_line_id',
                                'fave_agent_telegram',
                                'fave_agent_skype',
                                'fave_agent_zillow',
                                'fave_agent_realtor_com',
                                'fave_agent_facebook',
                                'fave_agent_twitter',
                                'fave_agent_linkedin',
                                'fave_agent_googleplus',
                                'fave_agent_tiktok',
                                'fave_agent_instagram',
                                'fave_agent_pinterest',
                                'fave_agent_youtube',
                                'fave_agent_vimeo',
                            ],
                        ],
                    ],
                ],
			]
		);

		$this->add_control(
            'agent_meta_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} .hzel-agent-meta-field' => 'color: {{VALUE}};',
                ],
            ]
        );

		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'agent_meta_typography',
                'selector' => '{{WRAPPER}} .hzel-agent-meta-field',
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_agent_meta_icon_style',
			[
				'label' => esc_html__( 'Icon', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions' => [
                    'terms' => [
                        [
                            'name' => 'agent_meta_field',
                            'operator' => 'in',
                            'value' => [
                                'fave_agent_whatsapp',
                                'fave_agent_line_id',
                                'fave_agent_telegram',
                                'fave_agent_skype',
                                'fave_agent_zillow',
                                'fave_agent_realtor_com',
                                'fave_agent_facebook',
                                'fave_agent_twitter',
                                'fave_agent_linkedin',
                                'fave_agent_googleplus',
                                'fave_agent_tiktok',
                                'fave_agent_instagram',
                                'fave_agent_pinterest',
                                'fave_agent_youtube',
                                'fave_agent_vimeo',
                            ],
                        ],
                    ],
                ],
			]
		);

		$this->add_control(
            'agent_meta_icon_color',
            [
                'label' => esc_html__( 'Color', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} .hzel-agent-social a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'agent_meta_icon_hover_color',
            [
                'label' => esc_html__( 'Color Hover', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::COLOR,
				'selectors' => [
                    '{{WRAPPER}} .hzel-agent-social a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

		$this->add_responsive_control(
            'agent_meta_icon_size',
            [
                'label' => esc_html__( 'Size', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'default' => [
					'unit' => 'px',
					'size' => 15,
				],
                'selectors' => [
                    '{{WRAPPER}} .hzel-agent-social a' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .hzel-agent-social a svg' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->end_controls_section();*/
		
	}

	protected function fields_mapping_for_theme_icons() {
		$icons = [
            'property_rooms' => ['fa_room', 'icon-real-estate-dimensions-plan-1', 'room'],
            'property_bedrooms' => ['fa_bed', 'icon-hotel-double-bed-1', 'bed'],
            'property_bathrooms' => ['fa_bath', 'icon-bathroom-shower-1', 'bath'],
            'property_garage' => ['fa_garage', 'icon-car-1', 'garage'],
            'property_size' => ['fa_area-size', 'icon-real-estate-dimensions-plan-1', 'area-size'],
            'property_land' => ['fa_land-area', 'icon-real-estate-dimensions-map', 'land-area'],
            'property_year' => ['fa_year-built', 'icon-calendar-3', 'year-built'],
            'property_id' => ['fa_property-id', 'icon-tags', 'property-id']
        ];
	    return $icons;
	}

	protected function render_theme_default_icon($settings) {
	    $icons = $this->fields_mapping_for_theme_icons();

	    $icon_class = isset($icons[$settings['property_meta_field']]) ? $icons[$settings['property_meta_field']][1] : '';

	    if ($icon_class) {
	        echo '<span class="hs-meta-widget-icon"><i class="houzez-icon ' . esc_attr($icon_class) . '"></i></span>';
	    }
	}

	protected function render_theme_fontawesome_icon($settings) {

	    $icons = $this->fields_mapping_for_theme_icons();

	    $icon_field = isset($icons[$settings['property_meta_field']]) ? $icons[$settings['property_meta_field']][0] : 'fa_'.$settings['property_meta_field'];

	    $icon_class = houzez_option($icon_field);

	    if (!$icon_class) {
	        return;
	    }

	    // Render the icon HTML
	    echo '<span class="hs-meta-widget-icon"><i class="' . esc_attr($icon_class) . '"></i></span>';
	}

	protected function render_theme_custom_icon($settings) {
		$icons = $this->fields_mapping_for_theme_icons();

	    $icon_field = isset($icons[$settings['property_meta_field']]) ? $icons[$settings['property_meta_field']][2] : $settings['property_meta_field'];

	    $icon_var = houzez_option($icon_field);

	    if ( empty( $icon_var['url']) ) {
	        return;
	    }

	    $alt = isset($icon_var['title']) ? $icon_var['title'] : '';
		echo '<span class="hs-meta-widget-icon"><img class="img-fluid" src="'.esc_url($icon_var['url']).'" width="16" height="16" alt="'.esc_attr($alt).'"></span>';

	}


	protected function render_theme_options_icon( $settings ) {
		$ticon_type = houzez_option('icons_type');

		if( $ticon_type === 'font-awesome') {
			$this->render_theme_fontawesome_icon($settings);
		} else if( $ticon_type === 'custom') {
			$this->render_theme_custom_icon($settings);
		} else {
			$this->render_theme_default_icon( $settings );
		}
	}

	protected function render_custom_icon( $settings ) { 
	?>
		<span class="hs-meta-widget-icon">
			<?php echo houzez_render_icon($settings['icon'], [ 'aria-hidden' => 'true' ] ); ?>
		</span>
	<?php
	}

	protected function render() {
		global $settings;
        $this->single_property_preview_query(); // Only for preview

        $settings = $this->get_settings_for_display();
        $meta_html = '';

        $this->add_render_attribute(
        	[
        		'property-meta-wrap' => [
        			'class' => [
        				'hs-meta-widget-module',
        				'hs-meta-widget-'.$settings['meta_layout']
        			]
        		]
        	]
        );

        $prefix = 'fave_';
        $meta_field = $settings['property_meta_field'];

        if( ! empty($meta_field) ) {
			$meta_value = get_post_meta( get_the_ID(), $prefix.$meta_field, true );
			
			if( ! empty( $meta_value ) ) { ?>
			<div <?php $this->print_render_attribute_string( 'property-meta-wrap' ); ?>>
				
				<?php 
				if( $settings['icon_type'] === 'default' ) {
					$this->render_theme_options_icon( $settings );
				} else if( $settings['icon_type'] === 'custom' ) {
					$this->render_custom_icon( $settings );
				}
				?>

				<?php if( ! empty( $settings['meta_label'] ) ) { ?>
				<span class="hs-meta-widget-title"><?php echo esc_attr($settings['meta_label']); ?></span>
				<?php } ?>

				<span class="hs-meta-widget-data"><?php echo esc_attr($meta_value);?></span>
			</div>
			<?php } ?>
			
		<?php
		}

		$this->reset_preview_query(); // Only for preview
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Property_Meta_Data );