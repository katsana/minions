<?php

namespace Minions;

use Illuminate\Support\Facades\Facade;

/**
 * @method \Minions\Http\Router rpc(string $method, string $handler, array $projects = ['*'])
 * @method array getRoutes()
 *
 * @see \Minions\Http\Router
 */
class Router extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'minions.router';
    }
}
