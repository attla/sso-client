<?php

use Illuminate\Support\Str;

$config = config();
$router = app('router');
$groupAs = $config->get('sso.route-group.as', 'sso.');

$router->group($config->get('sso.route-group', [
    'as'            => 'sso.',
    'prefix'        => '/sso',
    'namespace'     => 'Attla\\SSO\\Controllers',
    'controller'    => 'AuthController',
    'middleware'    => [
        'web',
    ],
]), function ($router) use ($config, $groupAs) {
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
            $router->name($route)->get('/' . $route, fn() => redirect($uri));
        }
    }
});

collect($config->get('sso.route-alias', []))
    ->filter()
    ->map(function ($aliases, $route) use ($router, $groupAs) {
        $aliases = is_array($aliases) ? $aliases : [$aliases];

        foreach (
            collect($aliases)->map(function ($alias) {
                return is_string($alias) ? trim($alias, '/') : '';
            })->filter()
            ->all() as $uri
        ) {
            if ($router->has($groupAs . $route)) {
                $router->name(Str::slug($uri))->get('/' . $uri, fn() => redirect(route($groupAs . $route)));
            }
        }
    });
