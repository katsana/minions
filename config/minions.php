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

    'id' => 'minion-server',

    /*
     |--------------------------------------------------------------------------
     | Projects
     |--------------------------------------------------------------------------
     |
     | List of applications this app will communicate with.
     |
     */

    'projects' => [
        'minion-client' => [
            'endpoint' => null,
            'token' => null,
            'signature' => null,
            'options' => [],
        ],
    ],
];
