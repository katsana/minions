<?php

namespace Minions\Server;

use Datto\JsonRpc\Evaluator;
use ErrorException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Pipeline\Pipeline;
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
     * @var array
     */
    protected $config = [];

    /**
     * List of available RPC routes.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Construct a new Minion.
     */
    public function __construct(Container $container, array $config)
    {
        $this->container = $container;
        $this->config = $config;
        $this->routes = $config['services'] ?? [];
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
