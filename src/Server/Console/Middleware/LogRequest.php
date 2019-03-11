<?php

namespace Minions\Server\Console\Middleware;

use Psr\Http\Message\ServerRequestInterface;

class LogRequest
{
    /**
     * Log request information.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable                                 $next
     *
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        echo \date('Y-m-d H:i:s').' '.$request->getMethod().' '.$request->getUri().PHP_EOL;

        return $next($request);
    }
}
