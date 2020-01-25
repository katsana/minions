<?php

namespace Minions;

use Illuminate\Support\Facades\Facade;

/**
 * @method \Minions\Client\Project project(string $project)
 * @method \React\Promise\PromiseInterface broadcast(string $project, \Minionts\Client\MessageInterface $message)
 * @method \React\EventLoop\LoopInterface getEventLoop()
 * @method \Minions\Client\Minion setEventLoop(\React\EventLoop\LoopInterface $eventLoop)
 *
 * @see \Minions\Client\Minion
 */
class Minion extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'minions.client';
    }

    /**
     * Construct a new Message.
     *
     * @param int|string|null $id
     */
    public static function message(string $method, array $parameters, $id = null): Client\Message
    {
        return new Client\Message($method, $parameters, $id ?? \uniqid());
    }

    /**
     * Construct a new Notification.
     */
    public static function notification(string $method, array $parameters): Client\Notification
    {
        return new Client\Notification($method, $parameters, $id);
    }
}
