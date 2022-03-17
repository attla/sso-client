<?php

// sso server uri
$server = 'http://localhost/sso/';
$redirect = 'http://localhost/';

return [
    'route' => array_map(function ($route) use ($server, $redirect) {
        return $server . $route . '?client=' . ($_SERVER['HTTP_HOST'] ?? '') . '&r=' . $redirect;
    }, [
        // identifies if the user is logged in and handles the return
        'identifier'    => 'identifier',
        // login page
        'login'         => 'login',
        // register page
        'register'      => 'register',
        // logout route
        'logout'        => 'logout',
    ]),
    'route-group' => [
        'as'            => 'sso.',
        'prefix'        => '/sso',
        'namespace'     => 'Attla\\SSO\\Controllers',
        'controller'    => 'AuthController',
        'middleware'    => [
            'web',
        ],
    ],
    'tll' => 31556926,
    'default_route' => 'home',
];
