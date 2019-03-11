<?php

namespace Minions\Server\Console;

use Error;
use Exception;
use Illuminate\Console\Command;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory as EventLoop;
use React\EventLoop\LoopInterface;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;
use Throwable;

class StartJsonRpcServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minions:serve';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $config = \array_merge([
            'host' => '0.0.0.0', 'port' => 8085, 'secure' => false,
        ], $this->laravel->get('config')->get('minions.server', []));

        $hostname = "{$config['host']}:{$config['port']}";

        $loop = EventLoop::create();

        $server = new HttpServer([
            new Middleware\LogRequest(),
            new Middleware\StatusPage(),
            function (ServerRequestInterface $request) {
                try {
                    return $this->laravel->make('minions.request')->handle($request)->asResponse();
                } catch (Exception | Throwable | Error $e) {
                    $this->error($e->getMessage());
                }
            },
        ]);

        $server->on('error', function ($e) {
            $this->error($e->getMessage());
        });

        if ($config['secure'] === true) {
            $this->bootSecuredServer($server, $loop, $hostname, $config['options'] ?? []);
        } else {
            $this->bootUnsecuredServer($server, $loop, $hostname);
        }

        $loop->run();
    }

    /**
     * Boot HTTPS Server.
     *
     * @param \React\Http\Server             $server
     * @param \React\EventLoop\LoopInterface $loop
     * @param string                         $hostname
     * @param array                          $options
     *
     * @return void
     */
    protected function bootSecuredServer(HttpServer $server, LoopInterface $loop, string $hostname, array $options): void
    {
        $server->listen(new SocketServer("tls://{$hostname}", $loop, $options));

        echo "Server running at https://{$hostname}\n";
    }

    /**
     * Boot HTTPS Server.
     *
     * @param \React\Http\Server             $server
     * @param \React\EventLoop\LoopInterface $loop
     * @param string                         $hostname
     *
     * @return void
     */
    protected function bootUnsecuredServer(HttpServer $server, LoopInterface $loop, string $hostname): void
    {
        $server->listen(new SocketServer($hostname, $loop));

        echo "Server running at http://{$hostname}\n";
    }
}
