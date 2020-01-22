JSON-RPC Communication for Laravel
===================

[![Build Status](https://travis-ci.org/katsana/minions.svg?branch=master)](https://travis-ci.org/katsana/minions)
[![Latest Stable Version](https://poser.pugx.org/katsana/minions/v/stable)](https://packagist.org/packages/katsana/minions)
[![Total Downloads](https://poser.pugx.org/katsana/minions/downloads)](https://packagist.org/packages/katsana/minions)
[![Latest Unstable Version](https://poser.pugx.org/katsana/minions/v/unstable)](https://packagist.org/packages/katsana/minions)
[![License](https://poser.pugx.org/katsana/minions/license)](https://packagist.org/packages/katsana/minions)
[![Coverage Status](https://coveralls.io/repos/github/katsana/minions/badge.svg?branch=master)](https://coveralls.io/github/katsana/minions?branch=master)

* [Installation](#installation)
    - [Configuration](#configuration)
    - [Installation for Client](#installation-for-client)
    - [Installation for Server](#installation-for-server)

## Installation

Minions can be installed via composer:

```
composer require "katsana/minions"
```

### Configuration

The package will automatically register a service provider.

Next, you need to publish the Minions configuration file:

```
php artisan vendor:publish --provider="Minions\MinionsServiceProvider" --tag="config"
```

### Installation for Client

To use Minions as a client, you need to install the following via Composer:

```
composer require "clue/buzz-react=^2.5"
```

#### Configurations

The following changes is meant for `config/minions.php`.

##### Set Project ID

Before continuing, you need to setup the Project ID which will be used by the servers to identify authorized RPC requests. To do set `minions.id` value:

```php
<?php

return [
    // ...
    
    'id' => 'client-project-id',
    
    // ...

];
```

##### Configure Project Servers

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

### Installation for Server

To use Minions as a server, you need to install the following via Composer:

```
composer require "react/http=^0.8.4"
```


#### Configurations

The following changes is meant for `config/minions.php`.

##### Set Project ID

Before continuing, you need to setup the Project ID which will be used by the servers to identify authorized RPC requests. To do set `minions.id` value:

```php
<?php

return [
    // ...
    
    'id' => 'server-project-id',
    
    // ...

];
```

##### Configure Project Clients

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


