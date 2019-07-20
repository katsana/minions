<?php

namespace Minions;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use React\EventLoop\LoopInterface;

class MinionsServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('minions.client', static function (Application $app) {
            return new Client\Minion(
                $app->make(LoopInterface::class),
                $app->make('config')->get('minions')
            );
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
