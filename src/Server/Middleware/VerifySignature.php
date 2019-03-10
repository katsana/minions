<?php

namespace Minions\Server;

use Closure;
use Psr\Http\Message\ServerRequestInterface;

class VerifySignature
{
    public function handle(ServerRequestInterface $request, Closure $next)
    {
        return $next($request);
    }
}
