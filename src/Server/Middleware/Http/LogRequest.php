<?php

namespace Minions\Server\Middleware\Http;

use Laravie\Stream\Logger;
use Psr\Http\Message\ServerRequestInterface;

class LogRequest
{
    /**
     * The console logger.
     *
     * @var \Laravie\Stream\Logger
     */
    protected $logger;

    /**
     * Construct log request middleware.
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Log request information.
     *
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $this->logger->info(
            \date('Y-m-d H:i:s').' '.$request->getMethod().' '.$request->getUri()
        );

        return $next($request);
    }
}
