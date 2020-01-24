<?php

namespace Minions\Server;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Minions\Concerns\Configuration;
use Orchestra\Canvas\Core\CommandsProvider;

class MinionsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    use CommandsProvider,
        Configuration;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('minions.commands.make-request', function (Container $app) {
            return new Console\MakeRpcRequest($this->presetForLaravel($app));
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                'minions.commands.make-request',
                Console\InstallServer::class,
                Console\StartJsonRpcServer::class,
            ]);
        }

        $this->bootRpcRoutes();
    }

    /**
     * Get the events that trigger this service provider to register.
     *
     * @return array
     */
    public function provides()
    {
        return ['minions.router'];
    }
}
