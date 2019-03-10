<?php

namespace Minions\Server\Middleware;

use Closure;
use Minions\Server\Message;

class VerifySignature
{
    /**
     * Handle middleware.
     *
     * @param \Minions\Server\Message $message
     * @param \Closure                $next
     *
     * @return mixed
     */
    public function handle(Message $message, Closure $next)
    {
        $message->validateRequestSignature();

        return $next($message);
    }
}
