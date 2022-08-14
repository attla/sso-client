<?php

return [

    // SSO server URL
    'server' => 'http://127.0.0.1/sso',

    // Endpoint URI or route name to be redirected after SSO authentication
    'redirect' => '/',

    // URI on the server to redirect
    // internalName => serverURI
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

    // Route group configuration
    'route-group' => [
        'as'            => 'sso.',
        'prefix'        => '/sso',
        'namespace'     => 'Attla\\SSO\\Controllers',
        'controller'    => 'AuthController',
        'middleware'    => [
            'web',
        ],
    ],

    // URI aliases for redirect to sso routes
    'route-alias' => [
        'identifier'    => [
            // 'alias-uri',
        ],
        'login'         => [],
        'register'      => [],
        'logout'        => [],
    ],

    // Determine if the user will be remembered
    'remember' => true,

    // Redirect if login attempt fails or user is logged out, accepts URI or route name
    'default_redirect' => '/home',

    // Determine if sso auth flow are only for guest users
    'only_guest' => false,

];
