<?php

namespace Minions\Server;

use Laravie\Stream\Logger;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;

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
     * The console logger.
     *
     * @var \Laravie\Stream\Logger
     */
    protected $logger;

    /**
     * Construct a new HTTP Server connector.
     *
     * @param string                         $hostname
     * @param \React\EventLoop\LoopInterface $eventLoop
     * @param \Laravie\Stream\Logger         $logger
     */
    public function __construct(string $hostname, LoopInterface $eventLoop, Logger $logger)
    {
        $this->hostname = $hostname;
        $this->eventLoop = $eventLoop;
        $this->logger = $logger;
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
            new Middleware\Http\LogRequest($this->logger),
            new Middleware\Http\StatusPage(),
            function (ServerRequestInterface $request) use ($router) {
                return $router->handle($request)->asResponse();
            },
        ]);

        if ($config['secure'] === true) {
            $this->bootSecuredServer($server, $config['options'] ?? []);
        } else {
            $this->bootUnsecuredServer($server);
        }

        return $server;
    }

    /**
     * Boot HTTPS Socket Server.
     *
     * @param \React\Http\Server $server
     * @param array              $options
     *
     * @return void
     */
    protected function bootSecuredServer(HttpServer $server, array $options): void
    {
        $server->listen(new SocketServer("tls://{$this->hostname}", $this->eventLoop, $options));

        $this->logger->info("Server running at https://{$this->hostname}\n");
    }

    /**
     * Boot HTTP Socket Server.
     *
     * @param \React\Http\Server $server
     *
     * @return void
     */
    protected function bootUnsecuredServer(HttpServer $server): void
    {
        $server->listen(new SocketServer($this->hostname, $this->eventLoop));

        $this->logger->info("Server running at http://{$this->hostname}\n");
    }
}
