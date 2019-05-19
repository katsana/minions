<?php

namespace Minions\Server;

use Datto\JsonRpc\Server;
use Illuminate\Contracts\Container\Container;

class Controller
{
    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The router.
     *
     * @var \Minions\Server\Router
     */
    protected $router;

    /**
     * Construct a new Evaluator.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @param \Minions\Server\Router $router
     */
    public function __construct(Container $container, Router $router)
    {
        $this->container = $container;
        $this->router = $router;
    }

    /**
     * Handle the request.
     *
     * @param \Minions\Server\Message $message
     *
     * @return \Minions\Server\Reply
     */
    public function handle(Message $message): Reply
    {
        $server = new Server(
            new Evaluator($this->container, $this->router->getRoutes(), $message)
        );

        return new Reply($server->reply($message->body()));
    }
}
