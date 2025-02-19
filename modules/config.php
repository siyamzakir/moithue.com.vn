<?php
require_once __DIR__ . '/../main-config.php';
require_once __DIR__ . '/DB.php';

/**
 * @descriptions all edited files
 *  modified:   .htaccess
 *  modified:   .vscode/settings.json
 *  modified:   NOTE.md
 *  modified:   modules/DB.php
 *  modified:   modules/config.php
 *  modified:   wp-config.php
 *  modified:   wp-content/plugins/elementor/core/base/providers/social-network-provider.php
 *  modified:   wp-content/plugins/houzez-crm/js/script.js
 *  modified:   wp-content/plugins/houzez-theme-functionality/elementor/widgets/single-agency/agency-meta.php
 *  modified:   wp-content/plugins/houzez-theme-functionality/extensions/meta-box/css/fontawesome/icons.json
 *  modified:   wp-content/themes/houzez/framework/functions/profile_functions.php
 *  modified:   wp-content/themes/houzez/framework/functions/property_functions.php
 *  modified:   wp-content/themes/houzez/js/custom.js
 *  modified:   wp-content/themes/houzez/single-property.php
 *  modified:   wp-content/themes/houzez/style.css
 *  modified:   wp-content/themes/houzez/template-parts/dashboard/board/deals/deal-item.php
 *  modified:   wp-content/themes/houzez/template-parts/dashboard/board/deals/main.php
 *  modified:   wp-content/themes/houzez/template-parts/dashboard/board/deals/new-deal-panel.php
 *  modified:   wp-content/themes/houzez/template-parts/dashboard/board/leads/lead-detail.php
 *  modified:   wp-content/themes/houzez/template-parts/dashboard/board/leads/main.php
 *  modified:   wp-content/themes/houzez/template-parts/dashboard/profile/social.php
 *  modified:   wp-content/themes/houzez/template-parts/dashboard/property/property-item.php
 *  modified:   wp-content/themes/houzez/template-parts/dashboard/submit/edit-property-form.php
 *  modified:   wp-content/themes/houzez/template-parts/footer/social.php
 *  modified:   wp-content/themes/houzez/template/user_dashboard_properties.php
 *  modified:   wp-content/uploads/redux/google_fonts.json
 */


function copyingOrOverridingChangesFilesFromMainThemeToChildTheme() {
    // Correct the base paths to include the full path from the server root
    $parent_theme_path = '/opt/lampp/htdocs/moithue/wp-content/themes/houzez/';
    $child_theme_path = '/opt/lampp/htdocs/moithue/wp-content/themes/houzez-child/';

    // List of files to copy
    $files_to_copy = [
        'framework/functions/profile_functions.php',
        'framework/functions/property_functions.php',
        'js/custom.js',
        // 'single-property.php',
        'template-parts/dashboard/board/deals/deal-item.php',
        'template-parts/dashboard/board/deals/main.php',
        'template-parts/dashboard/board/deals/new-deal-panel.php',
        'template-parts/dashboard/board/leads/lead-detail.php',
        'template-parts/dashboard/board/leads/main.php',
        'template-parts/dashboard/profile/social.php',
        'template-parts/dashboard/property/property-item.php',
        'template-parts/dashboard/submit/edit-property-form.php',
        'template-parts/footer/social.php',
        'template/user_dashboard_properties.php'
    ];

    $expected_dirs = [
        'wp-content/themes/houzez-child/template-parts/dashboard/board/deals',
        'wp-content/themes/houzez-child/template-parts/dashboard/board/leads',
        'wp-content/themes/houzez-child/template-parts/dashboard/profile',
        'wp-content/themes/houzez-child/template-parts/dashboard/property',
        'wp-content/themes/houzez-child/template-parts/dashboard/submit',
        'wp-content/themes/houzez-child/template-parts/footer',
        'wp-content/themes/houzez-child/template',
        'wp-content/themes/houzez-child/js',
        'wp-content/themes/houzez-child/framework/functions',
    ];

    // if nor exists dir create dir
    foreach ($expected_dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }

    // Perform the copy operation for each file
    foreach ($files_to_copy as $file) {
        $source = $parent_theme_path . $file;
        $destination = $child_theme_path . $file;
        $destination_dir = dirname($destination);

        // Create the directory if it does not exist
        if (!is_dir($destination_dir)) {
            mkdir($destination_dir, 0775, true);
        }

        // Copy the file
        if (!copy($source, $destination)) {
            echo "File copy failed: $file\n";
        } else {
            echo "File copied successfully: $file\n";
        }
    }
}

// Call the function
// copyingOrOverridingChangesFilesFromMainThemeToChildTheme();

?>