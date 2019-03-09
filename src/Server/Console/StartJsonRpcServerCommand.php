<?php

namespace Minions\Server\Console;

use React\Http\Response;
use Illuminate\Console\Command;
use React\Http\Server as HttpServer;
use React\EventLoop\Factory as EventLoop;
use Psr\Http\Message\ServerRequestInterface;

class StartJsonRpcServerCommand extends Command
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

        $loop = Factory::create();

        $server = new HttpServer(function (ServerRequestInterface $request) {
            //$message = $this->laravel->make('minions.service-resolver')->handle($request);
            $message = new \Minions\Server\Message();

            return new Response(
                $message->status(), $message->headers(), $message->body()
            );
        });

        $socket = new HttpServer($port, $loop);
        $server->listen($socket);

        echo "Server running at http://127.0.0.1:{$port}\n";

        $loop->run();
    }
}
