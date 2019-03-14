<?php

namespace Minions\Server\Console;

use Illuminate\Console\Command;
use Minions\Server\Connector;
use React\EventLoop\Factory as EventLoop;
use Illuminate\Database\DetectsLostConnections;

class StartJsonRpcServer extends Command
{
    use DetectsLostConnections;

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

        $eventLoop = EventLoop::create();

        $connector = new Connector("{$config['host']}:{$config['port']}", $eventLoop);

        $server = $connector->handle($this->laravel->make('minions.router'), $config);

        $server->on('error', function ($e) use ($eventLoop) {
            $this->error($e->getMessage());

            if ($this->causedByLostConnection($e)) {
                $eventLoop->stop();
                exit(0);
            }
        });

        $eventLoop->run();
    }
}
