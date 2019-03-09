<?php

namespace Minions\Server\Console;

use Illuminate\Console\Command;

class StartJsonRpcServerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minions:serve
        {--host=0.0.0.0}
        {--port=8001}';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Amp\Loop::run(function () {
            $sockets = [
                Socket\listen("{$this->option('host')}:{$this->option('port')}"),
                Socket\listen("[::]:{$this->option('port')}"),
            ];

            $server = new Server($sockets, new CallableRequestHandler(function (Request $request) {
                return new Response(Status::OK, [
                    "content-type" => "text/plain; charset=utf-8"
                ], "Hello, World!");
            }), new NullLogger);

            yield $server->start();

            // Stop the server gracefully when SIGINT is received.
            // This is technically optional, but it is best to call Server::stop().
            Amp\Loop::onSignal(SIGINT, function (string $watcherId) use ($server) {
                Amp\Loop::cancel($watcherId);
                yield $server->stop();
            });
        });
    }
}
