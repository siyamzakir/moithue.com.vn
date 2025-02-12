<?php

use Realtyna\Core\Utilities\SettingsField;

if (!defined('ABSPATH')) {
    exit;
}

SettingsField::select(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'pictures_size',
    'id' => 'mls-on-the-fly-settings-default-integration',
    'label' => __('Default Integration', 'realtyna-mls-on-the-fly'),
    'options' => [
        'full-size' => __('Full size', 'realtyna-mls-on-the-fly'),
        'thumbnail' => __('Thumbnail', 'realtyna-mls-on-the-fly'),
    ],
    'value' => $settings['pictures_size'] ?? 'thumbnail',
));


SettingsField::input(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'slider_pictures_height',
    'id' => 'mls-on-the-fly-settings-slider-pictures-height',
    'label' => __( 'Thumbnail size', 'realtyna-mls-on-the-fly' ),
    'type'  => 'number',
    'value' => $settings['slider_pictures_height'] ?? 250,
));
