<?php

namespace Minions\Server\Middleware\Http;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;

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
            return new Response(200, ['Content-Type' => 'text/plain'], 'OK');
        }

        return $next($request);
    }
}
