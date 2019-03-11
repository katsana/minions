<?php

namespace Minions\Server;

use Datto\JsonRpc\Evaluator as DattoEvaluator;
use Datto\JsonRpc\Exceptions\MethodException;
use Datto\JsonRpc\Server;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

class Evaluator implements DattoEvaluator
{
    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * List of services.
     *
     * @var array
     */
    protected $services = [];

    /**
     * Construct a new Evaluator.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @param array                                     $services
     */
    public function __construct(Container $container, array $services)
    {
        $this->container = $container;
        $this->services = $services;
    }

    /**
     * Handle the request.
     *
     * @param ServerRequestInterface $request
     *
     * @return \Minions\Server\Reply
     */
    public function handle(Message $message): Reply
    {
        $server = new Server($this);

        return new Reply($server->reply($message->body()));
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function evaluate($method, $arguments)
    {
        if (! array_key_exists($method, $this->services)) {
            throw new MethodException();
        }

        try {
            $handler = $this->container->make($this->services[$method]);
        } catch (BindingResolutionException | ReflectionException $e) {
            throw new MethodException();
        }

        return $handler($arguments);
    }
}
