<?php

return [
    // sso server uri
    'server' => 'http://127.0.0.1/sso',
    // endpoint uri or route name to be redirected after sso authentication
    'redirect' => 'http://127.0.0.1',
    'route' => [
        // identifies if the user is logged in and handles the return
        'identifier'    => 'identifier',
        // login page
        'login'         => 'login',
        // register page
        'register'      => 'register',
        // logout route
        'logout'        => 'logout',
    ],
    'route-group' => [
        'as'            => 'sso.',
        'prefix'        => '/sso',
        'namespace'     => 'Attla\\SSO\\Controllers',
        'controller'    => 'AuthController',
        'middleware'    => [
            'web',
        ],
    ],
    'route-alias' => [
        'identifier'    => [
            // 'alias-uri',
        ],
        'login'         => [],
        'register'      => [],
        'logout'        => [],
    ],
    'tll' => 525600,
    'default_route' => 'home',
    'only_guest' => false,
];
