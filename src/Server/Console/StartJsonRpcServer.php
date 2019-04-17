<?php

namespace Minions\Server\Console;

use Illuminate\Console\Command;
use Illuminate\Database\DetectsLostConnections;
use Laravie\Stream\Log\Console as Logger;
use Minions\Server\Connector;
use React\EventLoop\LoopInterface;

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

        $hostname = "{$config['host']}:{$config['port']}";

        $monolog = $this->laravel->make('log');
        $eventLoop = $this->laravel->make(LoopInterface::class);
        $logger = $this->laravel->make(Logger::class);

        $connector = new Connector($hostname, $eventLoop, $logger);

        $server = $connector->handle($this->laravel->make('minions.router'), $config);

        $server->on('error', function ($e) use ($eventLoop, $monolog) {
            $this->error($e->getMessage());
            $monolog->error((string) $e);

            if ($this->causedByLostConnection($e)) {
                $eventLoop->stop();
                exit(0);
            }
        });

        $eventLoop->run();
    }
}
