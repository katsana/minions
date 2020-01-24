<?php

namespace Minions\Server;

use Laravie\Stream\Logger;
use Minions\Http\Router;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;
use React\Http\Response;
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
     */
    public function __construct(string $hostname, LoopInterface $eventLoop, Logger $logger)
    {
        $this->hostname = $hostname;
        $this->eventLoop = $eventLoop;
        $this->logger = $logger;
    }

    /**
     * Create HTTP Server.
     */
    public function handle(Router $router, array $config): HttpServer
    {
        return $this->bootServer(
            new HttpServer($this->middlewares($router)), $config
        );
    }

    /**
     * HTTP request middlewares.
     */
    protected function middlewares(Router $router): array
    {
        return [
            new Middleware\Http\LogRequest($this->logger),
            new Middleware\Http\StatusPage(),
            static function (ServerRequestInterface $request) use ($router) {
                $reply = $router->handle($request);

                return new Response(
                    $reply->status(), $reply->headers(), $reply->body()
                );
            },
        ];
    }

    /**
     * Boot server either using HTTPS or HTTP.
     */
    protected function bootServer(HttpServer $server, array $config): HttpServer
    {
        if ($config['secure'] === true) {
            $this->bootSecuredServer($server, $config['options'] ?? []);
        } else {
            $this->bootUnsecuredServer($server);
        }

        return $server;
    }

    /**
     * Boot HTTPS Socket Server.
     */
    protected function bootSecuredServer(HttpServer $server, array $options): void
    {
        $server->listen(new SocketServer("tls://{$this->hostname}", $this->eventLoop, $options));

        $this->logger->info("Server running at https://{$this->hostname}\n");
    }

    /**
     * Boot HTTP Socket Server.
     */
    protected function bootUnsecuredServer(HttpServer $server): void
    {
        $server->listen(new SocketServer($this->hostname, $this->eventLoop));

        $this->logger->info("Server running at http://{$this->hostname}\n");
    }
}
