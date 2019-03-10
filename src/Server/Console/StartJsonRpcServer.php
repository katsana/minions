<?php

namespace Minions\Server\Console;

use Error;
use Exception;
use Illuminate\Console\Command;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory as EventLoop;
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
            try {
                return $this->laravel->make('minions.request')->handle($request)->asResponse();
            } catch (Exception | Throwable | Error $e) {
                $this->error($e->getMessage());
            }
        });

        $server->on('error', function ($e) {
            $this->error($e->getMessage());
        });

        $server->listen(new SocketServer($port, $loop));

        echo "Server running at http://127.0.0.1:{$port}\n";

        $loop->run();
    }
}
