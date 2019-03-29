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
     * @param \Minions\Server\Message $message
     *
     * @return \Minions\Server\Reply
     */
    public function handle(Message $message): Reply
    {
        $server = new Server(
            new Evaluator($this->container, $this->services, $message)
        );

        return new Reply($server->reply($message->body()));
    }
}
