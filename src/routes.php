<?php

$routes = [
    'auth' => [
        'method' => 'GET',
        'expression' => '/^\/$/',
        'protected' => false,
        'controller' => 'AuthController',
        'controller_method' => 'index'
    ],
    'auth.register' => [
        'method' => 'POST',
        'expression' => '/^\/register\/?$/',
        'protected' => false,
        'controller' => 'AuthController',
        'controller_method' => 'register'
    ],
    'auth.login' => [
        'method' => 'POST',
        'expression' => '/^\/login\/?$/',
        'protected' => false,
        'controller' => 'AuthController',
        'controller_method' => 'login'
    ],
    'email.sendEmail' => [
        'method' => 'POST',
        'expression' => '/^\/send-email\/?$/',
        'protected' => true,
        'controller' => 'EmailController',
        'controller_method' => 'create'
    ]
];
