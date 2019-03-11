<?php

namespace Minions\Server\Console\Middleware;

use Minions\Server\Reply;
use Psr\Http\Message\ServerRequestInterface;

class StatusPage
{
    /**
     * Show status page on `GET /`.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable                                 $next
     *
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        if ($request->getMethod() === 'GET' && $request->getUri()->getPath() === '/') {
            return (new Reply('OK'))->asResponse();
        }

        return $next($request);
    }
}
