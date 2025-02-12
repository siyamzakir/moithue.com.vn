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

		$repeater = new Repeater();
		$meta_fields = array();

        $meta_fields = [
            'property_rooms' => esc_html__( 'Rooms', 'houzez-theme-functionality' ),
            'property_bedrooms' => esc_html__( 'Bedrooms', 'houzez-theme-functionality' ),
            'property_bathrooms' => esc_html__( 'Bathrooms', 'houzez-theme-functionality' ),
            'property_size' => esc_html__( 'Area Size', 'houzez-theme-functionality' ),
            'property_land' => esc_html__( 'Land Area', 'houzez-theme-functionality' ),
            'property_garage' => esc_html__( 'Garages', 'houzez-theme-functionality' ),
            'property_year' => esc_html__( 'Built Year', 'houzez-theme-functionality' ),
            'property_status' => esc_html__( 'Property Status', 'houzez-theme-functionality' ),
            'property_type' => esc_html__( 'Property Type', 'houzez-theme-functionality' ),
            'property_label' => esc_html__( 'Property Label', 'houzez-theme-functionality' ),
            'property_country' => esc_html__( 'Property Country', 'houzez-theme-functionality' ),
            'property_city' => esc_html__( 'Property City', 'houzez-theme-functionality' ),
            'property_state' => esc_html__( 'Property State', 'houzez-theme-functionality' ),
            'property_area' => esc_html__( 'Property Area', 'houzez-theme-functionality' ),
            'property_id' => esc_html__( 'Property ID', 'houzez-theme-functionality' ),
            
        ];

        $meta_fields = array_merge($meta_fields, houzez_get_custom_field_for_elementor());

        /**
         * field types.
         */
        $meta_fields = apply_filters( 'houzez/property_meta_data', $meta_fields );

        $repeater->add_control(
            'field_type',
            [
                'label' => esc_html__( 'Field', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => $meta_fields,
                'default' => 'text',
            ]
        );

        $repeater->add_control(
            'label_singular',
            [
                'label' => esc_html__( 'Label', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'operator' => '!in',
                            'value' => [
                                'property_size',
                                'property_land',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'label_plural',
            [
                'label' => esc_html__( 'Label Plural', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::TEXT,
                'default' => '',
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'field_type',
                            'operator' => '!in',
                            'value' => [
                                'property_size',
                                'property_year',
                                'property_land',
                                'property_id',
                                'property_status',
                                'property_type',
                            ],
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'icon_type',
            [
                'label' => esc_html__( 'Icons From', 'houzez-theme-functionality' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'theme_options' => esc_html__( 'Theme Options ', 'houzez-theme-functionality' ),
                    'custom' => esc_html__('Custom Icon', 'houzez-theme-functionality'),
                    'none' => esc_html__('No Icon', 'houzez-theme-functionality'),
                ],
                'default' => 'theme_options',
            ]
        );

        $repeater->add_control(
			'meta_icon',
			[
				'label' => esc_html__( 'upload Icon', 'text-domain' ),
				'type' => Controls_Manager::ICONS,
				'condition' => [
                	'icon_type' => 'custom'
                ],
			]
		);

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
            'meta_fields',
            [
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ label_singular }}}',
            ]
        );

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

	protected function houzez_taxonomy_field($item, $i)
    {
        global $post;
        $output = "";

        $icon_type = $item['icon_type'];
        $meta_field = $item['field_type'];
        $label_plural = $item['label_plural'];
        $label_singular = $item['label_singular'];

        // Retrieve taxonomy data
        $field_data = houzez_taxonomy_simple_2($meta_field, $post->ID);
        $field_label = $label_singular;

        if (!empty($field_data)) {
            // Render icon if available
            $icon = houzez_render_icon($item['meta_icon'], ['aria-hidden' => 'true']);

            if ($icon_type === 'custom' && !empty($icon)) {
                $output .= '<span class="hs-meta-widget-icon">'. $icon . '</span>';
            }

            if( !empty( $field_label ) ) {
	            $output .= '<span class="hs-meta-widget-title">' . esc_attr($field_label) . '</span>';
	        }

            if( !empty( $field_data ) ) {
	            $output .= '<span class="hs-meta-widget-data">' . esc_attr($field_data) . '</span>';
	        }
        }

        return $output;
    }

	protected function houzez_meta_field($item, $i)
	{
	    global $post;
	    $output = "";

	    $icon_type = $item['icon_type'];
	    $meta_field = $item['field_type'];
	    $label_plural = $item['label_plural'];
	    $label_singular = $item['label_singular'];

	    // Change true to false to get an array of values
	    $field_data = get_post_meta($post->ID, 'fave_' . $meta_field, false);

	    // Check if $field_data is an array and join the values with a comma
	    if (is_array($field_data)) {
	        $field_data = implode(', ', $field_data);
	    }

	    // Determine the correct label (singular or plural)
	    $field_label = (is_array($field_data) && count($field_data) > 1 && !empty($label_plural)) ? $label_plural : $label_singular;

	    // Specific field data handling
	    switch ($meta_field) {
	        case 'property_size':
	            $field_data = houzez_get_listing_area_size($post->ID);
	            $field_label = houzez_get_listing_size_unit($post->ID);
	            break;
	        case 'property_land':
	            $field_data = houzez_get_land_area_size($post->ID);
	            $field_label = houzez_option('spl_lot', 'Lot') . ' ' . houzez_get_land_size_unit($post->ID);
	            break;
	    }

	    if (!empty($field_data)) {
	        $icon = houzez_render_icon($item['meta_icon'], ['aria-hidden' => 'true']);

	        if ($icon_type == 'custom' && !empty($icon)) {
	            $output .= '<span class="hs-meta-widget-icon">'. $icon . '</span>';
	        } elseif ($icon_type == 'theme_options') {
	            $output .= $this->get_meta_field_icon($meta_field);
	        }

	        if (!empty($field_label)) {
	            $output .= '<span class="hs-meta-widget-title">' . esc_attr($field_label) . '</span>';
	        }

	        if (!empty($field_data)) {
	            $output .= '<span class="hs-meta-widget-data">' . esc_attr($field_data) . '</span>';
	        }
	    }

	    return $output;
	}


    private function get_meta_field_icon($meta_field)
    {

        $icon_output = '';
        $icons = [
            'property_bedrooms' => ['fa_bed', 'icon-hotel-double-bed-1', 'bed'],
            'property_rooms' => ['fa_room', 'icon-real-estate-dimensions-plan-1', 'room'],
            'property_bathrooms' => ['fa_bath', 'icon-bathroom-shower-1', 'bath'],
            'property_garage' => ['fa_garage', 'icon-car-1', 'garage'],
            'property_size' => ['fa_area-size', 'icon-real-estate-dimensions-plan-1', 'area-size'],
            'property_land' => ['fa_land-area', 'icon-real-estate-dimensions-map', 'land-area'],
            'property_year' => ['fa_year-built', 'icon-calendar-3', 'year-built'],
            'property_id' => ['fa_property-id', 'icon-tags', 'property-id']
        ];

        if ( empty($meta_field) ) {
            return '';
        }

        $icon_type = houzez_option('icons_type');

        switch ($icon_type) {
            case 'font-awesome':
                $icon_field = $icons[$meta_field][0] ?? 'fa_'.$meta_field;
                $icon_output .= $this->append_icon_output($icon_field, houzez_option($icon_field));
                break;

            case 'custom':
                $icon_field = $icons[$meta_field][2] ?? $meta_field;
                $custom_icon = houzez_option($icon_field);
                if (!empty($custom_icon['url'])) {
                    $alt = $custom_icon['title'] ?? '';
                    $icon_output .= '<span class="hs-meta-widget-icon"><img class="img-fluid mr-1" src="' . esc_url($custom_icon['url']) . '" width="16" height="16" alt="' . esc_attr($alt) . '"></span>';
                }
                break;

            default:
                $icon_field = $icons[$meta_field][1] ?? '';
                $icon_output .= $this->append_icon_output($icon_field, 'houzez-icon');
                break;
        }

        return $icon_output;
    }

    private function append_icon_output($icon_field, $icon_class) {
        return !empty($icon_class) ? '<span class="hs-meta-widget-icon"><i class="' . $icon_class . ' ' . $icon_field . '"></i></span>' : '';
    }

    protected function render() {
	    global $settings;
	    $this->single_property_preview_query(); // Only for preview

	    $settings = $this->get_settings_for_display();

	    $this->add_render_attribute(
	        'property-meta-wrap', 'class', [
	            'hs-meta-widget-module',
	            'hs-meta-widget-' . $settings['meta_layout']
	        ]
	    );

	    foreach ($settings['meta_fields'] as $item_index => $item) {
	        $field_name = $item['field_type'];
	        $output = $this->get_field_output($field_name, $item, $item_index);
	        
	        if (!empty($output)) {
	            echo '<div ' . $this->get_render_attribute_string('property-meta-wrap') . '>';
	            echo $output;
	            echo '</div>';
	        }
	    }

	    $this->reset_preview_query(); // Only for preview
	}

	private function get_field_output($field_name, $item, $item_index) {
	    $taxonomy_fields = [
	        'property_type', 'property_status', 'property_label',
	        'property_city', 'property_country', 'property_state', 
	        'property_area'
	    ];

	    if (in_array($field_name, $taxonomy_fields)) {
	        return $this->houzez_taxonomy_field($item, $item_index);
	    } else {
	        return $this->houzez_meta_field($item, $item_index);
	    }
	}

}
Plugin::instance()->widgets_manager->register( new Houzez_Property_Meta_Data );