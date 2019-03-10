Json-RPC Communication for Laravel
===================

## Installation

Minions can be installed via composer:

```
composer require "katsana/minions"
```

The package will automatically register a service provider.

Next, you need to publish the Minions configuration file:

```
php artisan vendor:publish --provider="Minions\MinionsServiceProvider" --tag="config"
```

This is the default content of the config file that will be published as `config/minions.php`:

```php
<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Project ID
     |--------------------------------------------------------------------------
     |
     | Define the project ID of this application so any request by this app
     | will include the project id information allowing the server to
     | identify the request sources.
     */

    'id' => null,


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
        // 'add-user' => App\Jobs\Rpc\AddUserService::class,
    ],
];
```
