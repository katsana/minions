<?php

namespace Minions\Server\Middleware\Http;

use Psr\Http\Message\ServerRequestInterface;
use React\Stream\WritableStreamInterface;

class LogRequest
{
    /**
     * The writable stream.
     *
     * @var \React\Stream\WritableStreamInterface
     */
    protected $writableStream;

    /**
     * Construct log request middleware.
     *
     * @param \React\Stream\WritableStreamInterface $writableStream
     */
    public function __construct(WritableStreamInterface $writableStream)
    {
        $this->writableStream = $writableStream;
    }

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
        $this->writableStream->write(
            \date('Y-m-d H:i:s').' '.$request->getMethod().' '.$request->getUri().PHP_EOL
        );

        return $next($request);
    }
}
