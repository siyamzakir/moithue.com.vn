<?php


use Realtyna\Core\Utilities\SettingsField;

SettingsField::input(array(
    'parent_name' => 'mls-on-the-fly-settings',
    'child_name' => 'url_patterns',
    'id' => 'mls-on-the-fly-settings-url-patterns',
    'label' => __( 'URL Pattern', 'realtyna-mls-on-the-fly' ),
    'type'  => 'text',
    'value' => $settings['url_patterns'] ?? '',
));