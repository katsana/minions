<?php

namespace Minions\Server\Console;

use Illuminate\Console\Command;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory as EventLoop;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;

class StartJsonRpcServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minions:serve {--port=8080}';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $port = $this->option('port');

        $loop = EventLoop::create();

        $server = new HttpServer(function (ServerRequestInterface $request) {
            return $this->laravel->make('minions.request')
                        ->handle($request)
                        ->asResponse();
        });

        $socket = new SocketServer($port, $loop);
        $server->listen($socket);

        echo "Server running at http://127.0.0.1:{$port}\n";

        $loop->run();
    }
}
