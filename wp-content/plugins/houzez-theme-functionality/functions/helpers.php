<?php
namespace Elementor;
use Elementor\Core\Files\Assets\Svg\Svg_Handler;


if(!function_exists('houzez_realtor_social')) {
    function houzez_realtor_social() {
        $array = array(
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
            'fave_agency_whatsapp',
            'fave_agency_line_id',
            'fave_agency_telegram',
            'fave_agency_skype',
            'fave_agency_zillow',
            'fave_agency_realtor_com',
            'fave_agency_facebook',
            'fave_agency_twitter',
            'fave_agency_linkedin',
            'fave_agency_googleplus',
            'fave_agency_tiktok',
            'fave_agency_instagram',
            'fave_agency_pinterest',
            'fave_agency_youtube',
            'fave_agency_vimeo',
        );
        return $array;
    }
}

if( ! function_exists('houzez_render_icon') ) {
    function houzez_render_icon( $icon, $attributes = [], $tag = 'i' ) {

        if ( empty( $icon['library'] ) ) {
            return false;
        }

        $output = '';
        // handler SVG Icon
        if ( 'svg' === $icon['library'] ) {
            $output = houzez_render_svg_icon( $icon['value'] );
        } else {
            $output = houzez_render_icon_html( $icon, $attributes, $tag );
        }

        return $output;
    }
}

if( ! function_exists('houzez_render_icon_html') ) {
    function houzez_render_icon_html( $icon, $attributes = [], $tag = 'i' ) {
        $icon_types = \Elementor\Icons_Manager::get_icon_manager_tabs();
        if ( isset( $icon_types[ $icon['library'] ]['render_callback'] ) && is_callable( $icon_types[ $icon['library'] ]['render_callback'] ) ) {
            return call_user_func_array( $icon_types[ $icon['library'] ]['render_callback'], [ $icon, $attributes, $tag ] );
        }

        if ( empty( $attributes['class'] ) ) {
            $attributes['class'] = $icon['value'];
        } else {
            if ( is_array( $attributes['class'] ) ) {
                $attributes['class'][] = $icon['value'];
            } else {
                $attributes['class'] .= ' ' . $icon['value'];
            }
        }
        return '<' . $tag . ' ' . Utils::render_html_attributes( $attributes ) . '></' . $tag . '>';
    }
}

if( ! function_exists('houzez_render_svg_icon') ) {
    function houzez_render_svg_icon( $value ) {
        if ( ! isset( $value['id'] ) ) {
            return '';
        }

        return Svg_Handler::get_inline_svg( $value['id'] );
    }
}

if( ! function_exists('htb_get_template_type') ) {
    function htb_get_template_type($post_id = '') {
        $post = get_post($post_id);
        if($post && get_post_type($post) === 'fts_builder') {
            $meta = get_post_meta( $post_id, 'fts_template_type', true );
            if( ! empty( $meta ) ) {
                return $meta;
            } else{
                return 'content';
            }
        }
        return false;
    }
}