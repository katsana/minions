<?php

namespace Minions;

use Illuminate\Support\ServiceProvider;

class MinionsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            Server\Console\StartJsonRpcServerCommand::class,
        ]);
    }
}
