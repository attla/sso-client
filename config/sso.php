<?php

// sso server uri
$server = 'http://localhost/sso/';

return [
    'route' => array_map(function ($route) use ($server) {
        return $server . $route . '?client=' . ($_SERVER['HTTP_HOST'] ?? '');
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
];
