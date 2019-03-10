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
     | identify the source of a request.
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

### Installation for Client

To use Minions as client, you need to install the following via Composer:

```
composer require "graze/guzzle-jsonrpc=^3.0"
```


### Installation for Server

To use Minions as client, you need to install the following via Composer:

```
composer require "react/http=^0.8.4"
```

You also need to add the following service provider to `config/app.php` under `provides` configuration:

```php
Minions\Server\MinionsServiceProvider::class,
```

