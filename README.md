Json-RPC Communication for Laravel
===================

[![Build Status](https://travis-ci.org/katsana/minions.svg?branch=master)](https://travis-ci.org/katsana/minions)
[![Latest Stable Version](https://poser.pugx.org/katsana/minions/v/stable)](https://packagist.org/packages/katsana/minions)
[![Total Downloads](https://poser.pugx.org/katsana/minions/downloads)](https://packagist.org/packages/katsana/minions)
[![Latest Unstable Version](https://poser.pugx.org/katsana/minions/v/unstable)](https://packagist.org/packages/katsana/minions)
[![License](https://poser.pugx.org/katsana/minions/license)](https://packagist.org/packages/katsana/minions)
[![Coverage Status](https://coveralls.io/repos/github/katsana/minions/badge.svg?branch=master)](https://coveralls.io/github/katsana/minions?branch=master)

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
     | Server Configuration
     |--------------------------------------------------------------------------
     |
     | Define the server configuration including port number, SSL support etc.
     |
     */

    'server' => [
        'port' => env('MINION_SERVER_PORT', 8085),
        'secure' => env('MINION_SERVER_SECURE', false),
        'options' => [
            'tls' => array_filter([
                'local_cert' => env('MINION_SERVER_TLS_CERT', null),
                // 'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_SERVER
            ]),
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
            'options' => [],
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

