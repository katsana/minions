<?php

namespace Minions\Server;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class MinionsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('minions.router', function (Application $app) {
            return new Router($app, $app->make('config')->get('minions'));
        });

        $this->app->bind('minions.controller', function (Application $app) {
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
