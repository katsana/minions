<?php

namespace Minions\Server\Middleware;

use Closure;
use Minions\Server\Message;

class VerifyToken
{
    /**
     * Handle middleware.
     *
     * @return mixed
     */
    public function handle(Message $message, Closure $next)
    {
        $message->validateRequestToken();

        return $next($message);
    }
}
