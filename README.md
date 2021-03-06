JSON-RPC Communication for Laravel
===================

[![tests](https://github.com/katsana/minions/workflows/tests/badge.svg?branch=2.x)](https://github.com/katsana/minions/actions?query=workflow%3Atests+branch%3A2.x)
[![Latest Stable Version](https://poser.pugx.org/katsana/minions/v/stable)](https://packagist.org/packages/katsana/minions)
[![Total Downloads](https://poser.pugx.org/katsana/minions/downloads)](https://packagist.org/packages/katsana/minions)
[![Latest Unstable Version](https://poser.pugx.org/katsana/minions/v/unstable)](https://packagist.org/packages/katsana/minions)
[![License](https://poser.pugx.org/katsana/minions/license)](https://packagist.org/packages/katsana/minions)
[![Coverage Status](https://coveralls.io/repos/github/katsana/minions/badge.svg?branch=2.x)](https://coveralls.io/github/katsana/minions?branch=2.x)

* [Installation](#installation)
* [Setup](#setup)
    - [Setting Project ID](#setting-project-id)
    - [Configure Projects](#configure-projects)
* [Request Handler](#request-handler)
    - [Registering the route](#registering-the-route)
    - [Checking authorization](#checking-authorization)
    - [Validating the request](#validating-the-request)
* [Making a Request](#making-a-request)
* [Running the RPC Server](#running-the-rpc-server)

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

### Setting Project ID

Each project need to have a unique Project ID to be used to identify authorized RPC requests. You can set the project ID on `config/minions.php` configuration file:

```php
<?php

return [
    // ...
    
    'id' => 'project-id',
    
    // ...

];
```

### Configure Projects

Next, you need to setup the project clients and servers information:

```php
<?php

return [
    // ...
    
    'projects' => [
        'client-project-id' => [
            'token' => 'secret-token',
            'signature' => 'secret-signature',
        ],
        'server-project-id' => [
            'endpoint' => 'http://server-project-id:8084',
            'token' => 'another-secret-token',
            'signature' => 'another-secret-signature',
        ],
    ],

    // ...

];
```

* `endpoint` is only required for configurating server project connection from a client project. A server will never send request to a client project.
* Each project should have a pair of unique `token` and `secret`, this will be shared only by the client and server as a form of message verification.

#### Security 

`token` is used as an `Authorization` bearer token header for the request and `signature` is used to sign the message sent via the request. 

> For projects running on private intranet you may skip setting up `token` and `signature` by setting the value to `null`.

## Request Handler
 
To receive a request from a client, first we need to create a request handler on the server, for example let say we want to create a `Add` request.

```php
<?php

namespace App\JsonRpc;

use Minions\Http\Request;
use Minions\Http\ValidatesRequests;

class MathAdd
{
    use ValidatesRequests;

    /**
     * Handle the incoming request.
     *
     * @param  \Minions\Http\Request  $request
     *
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        return \array_sum($request->all());
    }

    /**
     * Authorize the incoming request.
     *
     * @param  \Minions\Http\Request  $request
     *
     * @return bool
     */
    public function authorize(Request $request): bool
    {
        return true;
    }
}
```

> You can use `php artisan minions:make MathAdd` to generate the base stub file `App\JsonRpc\MathAdd`.

### Registering the route

To register the route, all you need to do is add the request handler to `routes/rpc.php`:

```php
<?php

use Minions\Router;


Router::rpc('math.add', 'App\JsonRpc\MathAdd');
```

You can run the following command to stub `routes/rpc.php`:

```
php artisan vendor:publish --provider="Minions\Http\MinionsServiceProvider" --tag="routes"
```

### Checking authorization

You can check whether the project is authorized to use the request by overriding the `authorize()` method.

```php
/**
 * Authorize the incoming request.
 *
 * @param  \Minions\Http\Request  $request
 *
 * @return bool
 */
public function authorize(Request $request): bool
{
    return $request->id() === 'platform'; // only allow access from `platform`
}
```

### Validating the request

You can validate the request using Laravel Validation. **Minions** also introduce `Minions\Http\ValidatesRequests` trait which you can import and expose `validate()` and `validateWith()` method. e.g:

```php
<?php

namespace App\JsonRpc;

use App\User;
use Minions\Http\Request;
use Minions\Http\ValidatesRequests;

class User
{
    use ValidatesRequests;
    
    /**
     * Handle the incoming request.
     *
     * @param  \Minions\Http\Request  $request
     *
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'email' => ['required', 'email'],
        ]);

        return User::where('email', '=', $request['email'])->firstOrFail();
    }
}
```

## Making a Request

To make a request, you can create the following code:

```php
<?php

use Minions\Client\Message;
use Minions\Client\ResponseInterface;
use Minions\Minion;

Minion::broadcast('server-project-id', Minion::message(
    'math.add', [1, 2, 3, 4]
))->then(function (ResponseInterface $response) {
    assert(10, $response->getRpcResult());
});

Minion::run();
```

## Running the RPC Server

To run Minions RPC Server, you have two options:

* ReactPHP RPC Server using [katsana/minions-server](https://github.com/katsana/minions-server)
* Laravel Routing (Polyfill) RPC Server using [katsana/minions-polyfill](https://github.com/katsana/minions-polyfill)

Please go through each option documentation for installation and usages guide.

