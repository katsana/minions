<?php

namespace Minions;

use Illuminate\Support\Facades\Facade;

/**
 * @method \Minions\Http\Router rpc(string $method, string $handler, array $projects = ['*'])
 * @method array getRoutes()
 * @method \Minions\Server\Reply handle(\Psr\Http\Message\ServerRequestInterface $request)
 * @method \Minions\Http\Router project(string name, string $token, string $signature, ?string $endpoint = null, array $options = [])
 *
 * @see \Minions\Http\Router
 */
class Router extends Facade
{
    /**
     * Register route resolver.
     */
    public static function routeResolver(callable $resolver): void
    {
        Http\Router::routeResolver($resolver);
    }

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
