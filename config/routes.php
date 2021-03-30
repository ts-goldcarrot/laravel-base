<?php

return [
    'namespace' => 'App\Http\Controllers',

    'modules' => [
        'api' => [
            'directory' => 'api',
            'namespace' => 'api',
            'middleware' => ['api'],
            'prefix' => 'api',
        ]
    ],

];
