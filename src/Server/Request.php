<?php

namespace Minions\Server;

use Datto\JsonRpc\Evaluator;
use Datto\JsonRpc\Exceptions\Exception as JsonRpcException;
use Datto\JsonRpc\Server;
use Illuminate\Contracts\Container\Container;
use Illuminate\Pipeline\Pipeline;
use Minions\Exceptions\ProjectNotFound;
use Psr\Http\Message\ServerRequestInterface;

class Request
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
                        return $this->container->make('minions.evaluator')->handle($message);
                    });
        } catch (JsonRpcException $exception) {
            return $this->handleException($exception);
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
        if (is_null($project) || ! array_key_exists($project, $this->config['projects'])) {
            throw new ProjectNotFound($project);
        }

        return $this->config['projects'][$project];
    }

    /**
     * Handle Exception.
     *
     * @param \Datto\JsonRpc\Exceptions\Exception $exception
     *
     * @return \Minions\Server\Reply
     */
    protected function handleException(JsonRpcException $exception): Reply
    {
        $error = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ];

        $data = $exception->getData();

        if ($data !== null) {
            $error['data'] = $data;
        }

        return new Reply(json_encode([
            'jsonrpc' => Server::VERSION,
            'id' => null,
            'error' => $error,
        ]));
    }
}
