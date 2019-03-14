<?php

namespace Minions\Server\Console;

use Illuminate\Console\Command;
use Minions\Server\Connector;
use React\EventLoop\Factory as EventLoop;

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

        $loop = EventLoop::create();

        $server = new Connector("{$config['host']}:{$config['port']}", $loop);

        $server->handle($this->laravel->make('minions.request'), $config);

        $loop->run();
    }
}
