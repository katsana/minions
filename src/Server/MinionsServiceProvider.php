<?php

namespace Minions\Server;

use Illuminate\Support\ServiceProvider;

class MinionsServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('minions.evaluator', function (Application $app) {
            return new Evaluator($app, $app->make('config')->get('minions.services'));
        });

        $this->app->singleton('minions.request', function (Application $app) {
            return new Request($app, $app->make('config')->get('minions'));
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            Console\StartJsonRpcServer::class,
        ]);
    }
}
