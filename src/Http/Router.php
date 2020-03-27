<?php

namespace Minions\Http;

use Datto\JsonRpc\Evaluator;
use ErrorException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Pipeline\Pipeline;
use Minions\Configuration;
use Minions\Exceptions\ProjectNotFound;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class Router
{
    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Configuration.
     *
     * @var \Minions\Configuration
     */
    protected $config;

    /**
     * List of available RPC routes.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Route resolver.
     *
     * @var callable|null
     */
    protected static $routeResolver;

    /**
     * Construct a new Minion.
     */
    public function __construct(Container $container, Configuration $config)
    {
        $this->container = $container;
        $this->config = $config;
        $this->routes = $config['services'] ?? [];
    }

    /**
     * Register route resolver.
     */
    public static function routeResolver(callable $resolver): void
    {
        static::$routeResolver = $resolver;
    }

    /**
     * Set rpc route.
     *
     * @return $this
     */
    public function rpc(string $method, string $handler, array $projects = ['*'])
    {
        $this->routes[$method] = \compact('handler', 'projects');

        return $this;
    }

    /**
     * Get available routes.
     */
    public function getRoutes(): array
    {
        if (\is_callable(static::$routeResolver)) {
            \call_user_func(static::$routeResolver);

            static::$routeResolver = null;
        }

        return $this->routes;
    }

    /**
     * Handle request and return Evaluator for the request.
     *
     * @return \Minions\Server\Reply
     */
    public function handle(ServerRequestInterface $request)
    {
        $project = $request->getHeader('X-Request-ID')[0] ?? null;
        $app = $this->container;

        try {
            $config = $this->projectConfiguration($project);

            return (new Pipeline($app))
                    ->send(new Message($project, $config, $request))
                    ->through([
                        Middleware\VerifyToken::class,
                        Middleware\VerifySignature::class,
                    ])->then(static function (Message $message) use ($app) {
                        return $app->make('minions.controller')->handle($message);
                    });
        } catch (ErrorException | Throwable $exception) {
            return (new ExceptionHandler())->handle($exception);
        }
    }

    /**
     * Add or configure project.
     *
     * @return $this
     */
    public function project(
        string $name,
        string $token,
        string $signature,
        ?string $endpoint = null,
        array $options = []
    ) {
        $this->config['projects'][$project] = \compact('token', 'signature', 'endpoint', 'options');

        return $this;
    }

    /**
     * Get configuration for a project.
     */
    protected function projectConfiguration(?string $project): array
    {
        if (empty($project) || ! isset($this->config['projects'][$project])) {
            throw new ProjectNotFound($project);
        }

        return $this->config['projects'][$project];
    }
}
