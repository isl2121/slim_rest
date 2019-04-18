<?php

/*  제작자 : 진승규
*   제작일 : 2019-04-17
*   세션 참고 : https://github.com/adbario/slim-secure-session-middleware
*/


$settings = [
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => true,
        'db' => [
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'database'  => '',
            'username'  => '',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]
    ],'session' => [
        // lifetime 이 세션유지 [분]
        'name'           => 'slim_session',
        'lifetime'       => 60,
        'path'           => '/',
        'domain'         => null,
        'secure'         => false,
        'httponly'       => true,

        // Set session cookie path, domain and secure automatically
        'cookie_autoset' => true,

        // Path where session files are stored, PHP's default path will be used if set null
        'save_path'      => null,

        // Session cache limiter
        'cache_limiter'  => 'nocache',

        // Extend session lifetime after each user activity
        'autorefresh'    => false,

        // Encrypt session data if string is set
        'encryption_key' => null,

        // Session namespace
        'namespace'      => 'slim_app'
    ]
];
