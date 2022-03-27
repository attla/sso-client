<?php

$config = config();

app('router')->group($config->get('sso.route-group', [
    'as'            => 'sso.',
    'prefix'        => '/sso',
    'namespace'     => 'Attla\\SSO\\Controllers',
    'controller'    => 'AuthController',
    'middleware'    => [
        'web',
    ],
]), function ($router) use ($config) {
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

    $groupAs = $config->get('sso.route-group.as', 'sso.');

    foreach (config('sso.route', []) as $route => $uri) {
        if (!$router->has($groupAs . $route)) {
            $router->name($route)->get('/' . $route, fn() => redirect($uri));
        }
    }
});
