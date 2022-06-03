<?php

use Illuminate\Support\Str;

$config = config();
$router = app('router');
$groupAs = $config->get('sso.route-group.as', 'sso.');
$redirect = $config->get('sso.only_guest', false) && !\Auth::guest()
    ? $config->get('sso.redirect', '/') : '';

$router->group($config->get('sso.route-group', [
    'as'            => 'sso.',
    'prefix'        => '/sso',
    'namespace'     => 'Attla\\SSO\\Controllers',
    'controller'    => 'AuthController',
    'middleware'    => [
        'web',
    ],
]), function ($router) use ($config, $groupAs, $redirect) {
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

    foreach ($config->get('sso.route', []) as $route => $uri) {
        if (!$router->has($groupAs . $route)) {
            $router->name($route)->get('/' . $route, fn() => redirect($redirect ? $redirect : $uri));
        }
    }
});

collect($config->get('sso.route-alias', []))
    ->filter()
    ->map(function ($aliases, $route) use ($router, $groupAs, $redirect) {
        $aliases = is_array($aliases) ? $aliases : [$aliases];
        $routeName = $groupAs . $route;

        foreach (
            collect($aliases)->map(function ($alias) {
                return is_string($alias) ? trim($alias, '/') : '';
            })->filter()
            ->all() as $uri
        ) {
            if (
                $router->has($routeName)
                && !$router->has($uri = Str::slug($uri))
            ) {
                $router->name($uri)->get('/' . $uri, fn() => redirect(
                    $redirect && !in_array($route, ['callback', 'logout'])
                        ? $redirect
                        : route($routeName)
                ));
            }
        }
    });
