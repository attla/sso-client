<?php

namespace Attla\SSO;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the service provider
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'sso');
    }

    /**
     * Bootstrap the application events
     *
     * @return void
     */
    public function boot()
    {
        // Config
        $this->publishes([
            $this->configPath() => $this->app->configPath('sso.php'),
        ], 'attla/sso/config');

        // Migrations
        $migrationsPath = __DIR__ . '/../database/migrations';
        $this->publishes([
            $migrationsPath => $this->app->databasePath('migrations'),
        ], 'attla/sso/migrations');
        $this->loadMigrationsFrom($migrationsPath);
    }

    /**
     * Get config path
     *
     * @param bool
     */
    protected function configPath()
    {
        return __DIR__ . '/../config/sso.php';
    }
}
