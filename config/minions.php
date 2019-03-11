<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Project ID
     |--------------------------------------------------------------------------
     |
     | Define the project ID of this application so any request by this app
     | will include the project id information allowing the server to
     | identify the source of a request.
     */

    'id' => null,

    'server' => [
        'port' => 8085,
        'secure' => false,
        'options' => [
            'tls' => [
                'local_cert' => env('MINION_SERVER_TLS_CERT'),
                // 'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_SERVER
            ],
        ],
    ],


    /*
     |--------------------------------------------------------------------------
     | Projects
     |--------------------------------------------------------------------------
     |
     | List of applications this app will communicate with.
     |
     */

    'projects' => [
        'platform' => [
            'endpoint' => null,
            'token' => null,
            'signature' => null,
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Services
     |--------------------------------------------------------------------------
     |
     | List of services this app can handle as JSON-RPC server.
     |
     */

    'services' => [
        // 'add-user' => App\JsonRpc\AddUserService::class,
    ],
];
