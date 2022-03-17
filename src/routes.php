<?php

app('router')->group(app('config')->get('sso.route-group', [
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
});
