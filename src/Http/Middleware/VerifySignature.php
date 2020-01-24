<?php

namespace Minions\Http\Middleware;

use Closure;
use Minions\Http\Message;

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
