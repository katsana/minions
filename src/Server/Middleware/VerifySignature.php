<?php

namespace Minions\Server\Middleware;

use Closure;
use Minions\Server\Message;

class VerifySignature
{
    /**
     * Handle middleware.
     *
     * @return mixed
     */
    public function handle(Message $message, Closure $next)
    {
        $message->validateRequestSignature();

        return $next($message);
    }
}
