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
     * Construct a new Minion.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @param array                                     $config
     */
    public function __construct(Container $container, array $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    /**
     * Handle request and return Evaluator for the request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Minions\Server\Reply
     */
    public function handle(ServerRequestInterface $request)
    {
        $project = $request->getHeader('X-Request-ID')[0] ?? null;

        try {
            $config = $this->projectConfiguration($project);

            return (new Pipeline($this->container))
                    ->send(new Message($project, $config, $request))
                    ->through([
                        Middleware\VerifyToken::class,
                        Middleware\VerifySignature::class,
                    ])->then(function (Message $message) {
                        return $this->container->make('minions.controller')->handle($message);
                    });
        } catch (ErrorException | Throwable $exception) {
            return (new ExceptionHandler())->handle($exception);
        }
    }

    /**
     * Get configuration for a project.
     *
     * @param string|null $project
     *
     * @return array
     */
    protected function projectConfiguration(?string $project): array
    {
        if (\is_null($project) || ! \array_key_exists($project, $this->config['projects'])) {
            throw new ProjectNotFound($project);
        }

        return $this->config['projects'][$project];
    }
}
