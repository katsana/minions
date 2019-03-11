<?php

namespace Minions\Server;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class MinionsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @deprecated Implement the \Illuminate\Contracts\Support\DeferrableProvider interface instead. Will be removed in Laravel 5.9.
     *
     * @var bool
     */
    protected $defer = true;

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

    /**
     * Get the events that trigger this service provider to register.
     *
     * @return array
     */
    public function provides()
    {
        return ['minions.evaluator', 'minions.request'];
    }
}
