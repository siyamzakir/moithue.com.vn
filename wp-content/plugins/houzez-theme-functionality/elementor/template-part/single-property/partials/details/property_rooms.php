<?php
global $settings;
$property_rooms = houzez_get_listing_data('property_rooms');
$rooms_label = ($property_rooms > 1 ) ? $settings['rooms_title'] : $settings['room_title'];

if( !empty( $property_rooms ) ) {
    echo '<li>
            <strong>'.esc_attr($rooms_label).'</strong> 
            <span>'.esc_attr( $property_rooms ).'</span>
        </li>';
}