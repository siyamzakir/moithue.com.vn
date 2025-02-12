<?php

namespace Realtyna\MlsOnTheFly;

use Realtyna\MlsOnTheFly\AdminPages\MlsOnTheFlyAdminPage;
use Realtyna\MlsOnTheFly\Boot\App;
use Realtyna\MlsOnTheFly\Boot\Log;
use Realtyna\MlsOnTheFly\Components\CloudPost\CloudPostComponent;
use Realtyna\MlsOnTheFly\Components\Updater\UpdaterComponent;
use Realtyna\MlsOnTheFly\Database\CreateCacheTable;
use Realtyna\MlsOnTheFly\Database\CreateRFMappingsTable;
use Realtyna\MlsOnTheFly\Database\DeleteRFTermsTable;
use Realtyna\MlsOnTheFly\Database\UpdateRFMappingsTableAddUniqueIndexes;
use Realtyna\MlsOnTheFly\Settings\Settings;
use Realtyna\Core\StartUp;


class Main extends StartUp
{


    protected function components(): void
    {
        $this->addComponent(CloudPostComponent::class);
        $this->addComponent(UpdaterComponent::class);
    }

    protected function adminPages(): void
    {
        $this->addAdminPage(MlsOnTheFlyAdminPage::class);
    }

    protected function boot(): void
    {
        // Set the container in the App class for global access.
        App::setContainer($this->container);
        if($this->config->get('log.active')){
            Log::init($this->config->get('log.path'), $this->config->get('log.level'));
        }
    }

    /**
     * Check plugin requirements before activation.
     *
     * @return bool True if requirements are met, false otherwise.
     */
    public function requirements(): bool
    {
        return true;
    }

    /**
     */
    public function activation(): void
    {
        // Define the old plugin slug and directory
        $old_plugin_slug = 'realtyna-mls-on-the-fly/realtyna-mls-on-the-fly.php';
        $old_plugin_dir = WP_PLUGIN_DIR . '/realtyna-mls-on-the-fly';

        // Deactivate the old plugin if it's active
        if (is_plugin_active($old_plugin_slug)) {
            deactivate_plugins($old_plugin_slug);
        }

        // Delete the old plugin directory if it exists
        if (file_exists($old_plugin_dir)) {
            // Recursively delete the old plugin directory
            mls_on_the_fly_delete_directory($old_plugin_dir);
        }

        $this->migrate();
    }

    public function deactivation()
    {
    }

    public static function uninstallation(): void
    {
        Settings::delete_settings();
        self::rollback();
    }

    protected function migrations(): void
    {
        $this->addMigration(DeleteRFTermsTable::class);
        $this->addMigration(CreateRFMappingsTable::class);
        $this->addMigration(UpdateRFMappingsTableAddUniqueIndexes::class);
        $this->addMigration(CreateCacheTable::class);
    }
}