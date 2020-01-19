<?php

namespace Minions\Server;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Minions\Concerns\Configuration;

class MinionsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    use Configuration;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('minions.router', function (Container $app) {
            return new Router($app, $this->useConfigurationFrom($app));
        });

        $this->app->bind('minions.controller', static function (Container $app) {
            return new Controller($app, $app->make('minions.router'));
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
                Console\MakeRpcRequest::class,
                Console\StartJsonRpcServer::class,
            ]);
        }
    }

    /**
     * Get the events that trigger this service provider to register.
     *
     * @return array
     */
    public function provides()
    {
        return ['minions.controller', 'minions.router'];
    }
}
