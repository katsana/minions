<?php

namespace Minions\Server\Middleware;

use Closure;
use Psr\Http\Message\ServerRequestInterface;

class VerifyToken
{
    public function handle(ServerRequestInterface $request, Closure $next)
    {
        return $next($request);
    }
}
