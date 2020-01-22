JSON-RPC Communication for Laravel
===================

[![Build Status](https://travis-ci.org/katsana/minions.svg?branch=master)](https://travis-ci.org/katsana/minions)
[![Latest Stable Version](https://poser.pugx.org/katsana/minions/v/stable)](https://packagist.org/packages/katsana/minions)
[![Total Downloads](https://poser.pugx.org/katsana/minions/downloads)](https://packagist.org/packages/katsana/minions)
[![Latest Unstable Version](https://poser.pugx.org/katsana/minions/v/unstable)](https://packagist.org/packages/katsana/minions)
[![License](https://poser.pugx.org/katsana/minions/license)](https://packagist.org/packages/katsana/minions)
[![Coverage Status](https://coveralls.io/repos/github/katsana/minions/badge.svg?branch=master)](https://coveralls.io/github/katsana/minions?branch=master)

* [Installation](#installation)
* [Setup](#setup)
    - [Setup for Server](#setup-for-server)
    - [Setup for Client](#setup-for-client)
* [Request Handler](#request-handler)
* [Making a Request](#making-a-request)

## Installation

Minions can be installed via composer:

```
composer require "katsana/minions"
```

## Setup

The package will automatically register a service provider.

Next, you need to publish the Minions configuration file:

```
php artisan vendor:publish --provider="Minions\MinionsServiceProvider" --tag="config"
```

### Setup for Server

To use Minions as a server, you need to install the following via Composer:

```
composer require "react/http=^0.8.4"
```

The following changes is meant for `config/minions.php`.

#### Install

First, execute the following command:

    php artisan minions:install-server

> This will create `routes/rpc.php` route file to manage all JSON-RPC endpoint available from the server.

#### Set Project ID

Before continuing, you need to setup the Project ID which will be used by the servers to identify authorized RPC requests. To do set `minions.id` value:

```php
<?php

return [
    // ...
    
    'id' => 'server-project-id',
    
    // ...

];
```

#### Configure Project Clients

Next, you need to setup the project client credentials:

```php
<?php

return [
    // ...
    
    'projects' => [
        'client-project-id' => [
            'token' => 'secret-token',
            'signature' => 'secret-signature',
        ],
    ],

    // ...

];
```

> A client endpoint is not required because server will never need to make a request to a client.

### Setup for Client

To use Minions as a client, you need to install the following via Composer:

```
composer require "clue/buzz-react=^2.5"
```

The following changes is meant for `config/minions.php`.

#### Set Project ID

Before continuing, you need to setup the Project ID which will be used by the servers to identify authorized RPC requests. To do set `minions.id` value:

```php
<?php

return [
    // ...
    
    'id' => 'client-project-id',
    
    // ...

];
```

#### Configure Project Servers

Next, you need to setup the project servers endpoint and credentials.

```php
<?php

return [
    // ...
    
    'projects' => [
        'server-project-id' => [
            'endpoint' => 'http://rpc.server-project-id',
            'token' => 'secret-token',
            'signature' => 'secret-signature',
        ],
    ],

    // ...

];
```

## Request Handler
 
To receive a request from a client, first we need to create a request handler on the server, for example let say we want to create a `Add` request.

```php
<?php

namespace App\JsonRpc;

use Minions\Server\Message;

class Add
{
    /**
     * Handle the incoming request.
     *
     * @param  array  $arguments
     * @param  \Minions\Server\Message  $message
     *
     * @return array
     */
    public function __invoke(array $arguments, Message $message): array
    {
        return \array_sum($arguments);
    }
}
```

> You can use `php artisan minions:make Add` to generate the base stub file `App\JsonRpc\Add`.

### Registering the route

To register the route, all you need to do is add the request handler to `routes/rpc.php`:

```php
<?php

use Minions\Router;


Router::rpc('math.add', 'App\JsonRpc\Add');
```

### Running the server



## Making a Request

To make a request, you can create the following code:

```php
<?php

use Minions\Client\Message;
use Minions\Client\ResponseInterface;
use Minions\Minion;

Minion::broadcast('server-project-id', new Message(
    'math.add', [1, 2, 3, 4]
))->then(function (ResponseInterface $response) {
    assert(10, $response->getRpcResult());
});

Minion::run();
```

## Starting the RPC server

Minions behind the hood run using ReactPHP running from Artisan command. You can start the RPC server by issuing the artisan command:

    php artisan minions:serve

### Using a different port

The default port of the RPC server is `8085`. You may set a different port by updating the environment file (or `config/minions.php`).

```ini
MINION_SERVER_PORT=8081
```

### Restricting the listening host

By default, the RPC server will listen on `127.0.0.1` and will allow incoming connections from local networks. If you want to expose this, you set a different host updating the environment file (or `config/minions.php`).

```ini
MINION_SERVER_HOST='0.0.0.0'
```

### Keeping the socket server running with supervisord

The `minions:serve` daemon needs to always be running in order to accept connections. This is a prime use case for `supervisor`, a task runner on Linux.

First, make sure `supervisor` is installed.

    # On Debian / Ubuntu
    apt install supervisor

    # On Red Hat / CentOS
    yum install supervisor
    systemctl enable supervisor

Once installed, add a new process that supervisor needs to keep running. You place your configurations in the `/etc/supervisor/conf.d` (Debian/Ubuntu) or `/etc/supervisord.d` (Red Hat/CentOS) directory.

Within that directory, create a new file called `minions.conf`.

```
[program:minion]
command=/usr/bin/php /home/project/artisan minions:serve
numprocs=1
autostart=true
autorestart=true
```

Once created, instruct `supervisor` to reload it's configuration files (without impacting the already running `supervisor` jobs).

    supervisorctl update
    supervisorctl start minion

Your RPC server should now be running (you can verify this with `supervisorctl status`). If it were to crash, `supervisor` will automatically restart it.

Please note that, by default, `supervisor` will force a maximum number of open files onto all the processes that it manages. This is configured by the `minfds` parameter in `supervisord.conf`.

If you want to increase the maximum number of open files, you may do so in `/etc/supervisor/supervisord.conf` (Debian/Ubuntu) or `/etc/supervisord.conf` (Red Hat/CentOS):

    [supervisord]
    minfds=10240; (min. avail startup file descriptors;default 1024)

After changing this setting, you'll need to restart the `supervisor` process (which in turn will restart all your processes that it manages).
