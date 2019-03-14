<?php

namespace Minions\Server;

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;
use React\Stream\WritableResourceStream;

class Connector
{
    /**
     * The server hostname.
     *
     * @var string
     */
    protected $hostname;

    /**
     * The event loop implementation.
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $eventLoop;

    /**
     * The writable stream.
     *
     * @var \React\Stream\WritableResourceStream
     */
    protected $writableStream;

    /**
     * Construct a new HTTP Server connector.
     *
     * @param string                         $hostname
     * @param \React\EventLoop\LoopInterface $loop
     */
    public function __construct(string $hostname, LoopInterface $loop)
    {
        $this->hostname = $hostname;
        $this->eventLoop = $eventLoop;
        $this->writableStream = new WritableResourceStream(STDOUT, $eventLoop);
    }

    /**
     * Create HTTP Server.
     *
     * @param \Minions\Server\Router $router
     * @param array                  $config
     *
     * @return \React\Http\Server
     */
    public function handle(Router $router, array $config): HttpServer
    {
        $server = new HttpServer([
            new Middleware\Http\LogRequest($this->writableStream),
            new Middleware\Http\StatusPage(),
            function (ServerRequestInterface $request) use ($router) {
                return $router->handle($request)->asResponse();
            },
        ]);

        if ($config['secure'] === true) {
            $this->bootSecuredServer($server, $hostname, $config['options'] ?? []);
        } else {
            $this->bootUnsecuredServer($server, $hostname);
        }

        return $server;
    }

    /**
     * Boot HTTPS Server.
     *
     * @param \React\Http\Server $server
     * @param string             $hostname
     * @param array              $options
     *
     * @return void
     */
    protected function bootSecuredServer(HttpServer $server, string $hostname, array $options): void
    {
        $server->listen(new SocketServer("tls://{$hostname}", $this->eventLoop, $options));

        $this->writableStream->write("Server running at https://{$hostname}\n");
    }

    /**
     * Boot HTTPS Server.
     *
     * @param \React\Http\Server $server
     * @param string             $hostname
     *
     * @return void
     */
    protected function bootUnsecuredServer(HttpServer $server, string $hostname): void
    {
        $server->listen(new SocketServer($hostname, $this->eventLoop));

        $this->writableStream->write("Server running at http://{$hostname}\n");
    }
}
