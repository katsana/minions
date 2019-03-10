<?php

namespace Minions;

use Illuminate\Contracts\Foundation\Application;
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
        $this->app->singleton('minions', function (Application $app) {
            return new Minion($app->make('config')->get('minions'));
        });
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/minions.php' => \config_path('minions.php'),
        ], 'config');
    }
}
