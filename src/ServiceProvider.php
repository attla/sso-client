<?php

namespace Attla\SSO;

use Illuminate\Http\Request;
use Illuminate\Support\{
    ServiceProvider as BaseServiceProvider,
    Str,
    Facades\Auth
};

class ServiceProvider extends BaseServiceProvider
{
    protected $router;
    protected $groupAs;

    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;

        $this->router = app('router');
        $this->groupAs = Resolver::getConfig('sso.route-group.as', 'sso.');
    }

    /**
     * Register the service provider
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'sso');
        $this->loadRoutes();
        $this->loadRoutesAliases();
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
     * Load SSO routes
     *
     * @return void
     */
    protected function loadRoutes()
    {
        $this->router->group(Resolver::getConfig('sso.route-group', [
            'as'            => 'sso.',
            'prefix'        => '/sso',
            'namespace'     => 'Attla\\SSO\\Controllers',
            'controller'    => 'AuthController',
            'middleware'    => [
                'web',
            ],
        ]), function ($router) {
            foreach (
                [
                    'callback',
                    'logout',
                ] as $action
            ) {
                $router->get('/' . $action, [
                    'uses' => $action,
                    'as' => $action,
                ]);
            }

            foreach (Resolver::getConfig('sso.route', []) as $route => $uri) {
                $route = trim(trim($route), '/');

                if (!$router->has($this->groupAs . $route)) {
                    $router->name($route)->get('/' . $route, fn(Request $request) => redirect(
                        $this->onlyGuest()
                            ? Resolver::redirect()
                            : Resolver::link($uri, Resolver::getRedirectFromRequest($request))
                    ));
                }
            }
        });
    }

    /**
     * Get config path
     *
     * @return void
     */
    protected function loadRoutesAliases()
    {
        collect(Resolver::getConfig('sso.route-alias', []))
            ->filter()
            ->map(function ($aliases, $route) {
                $aliases = is_array($aliases) ? $aliases : [$aliases];
                $routeName = $this->groupAs . $route;

                foreach (
                    collect($aliases)->map(function ($alias) {
                        return is_string($alias)
                            ? trim(trim($alias), '/')
                            : '';
                    })->filter()
                    ->all() as $uri
                ) {
                    if (
                        $this->router->has($routeName)
                        && !$this->router->has($uri = Str::slug($uri))
                    ) {
                        $this->router->name($uri)->get('/' . $uri, fn(Request $request) => redirect(
                            !in_array($route, ['callback', 'logout']) && $this->onlyGuest()
                                ? Resolver::redirect()
                                : route($routeName, [
                                    'r' => Resolver::getRedirectFromRequest($request),
                                ])
                        ));
                    }
                }
            });
    }

    /**
     * Get config path
     *
     * @return bool
     */
    protected function configPath()
    {
        return __DIR__ . '/../config/sso.php';
    }

    /**
     * Determine if the route are only for guest users
     *
     * @return bool
     */
    protected function onlyGuest()
    {
        return Resolver::getConfig('sso.only_guest', false) && !Auth::guest();
    }
}
