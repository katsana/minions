<?php

namespace Minions\Http\Middleware;

use Closure;
use Minions\Http\Message;

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
