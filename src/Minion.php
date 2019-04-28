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
}
