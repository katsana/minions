<?php

namespace Minions;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use React\EventLoop\LoopInterface;

class MinionsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('minions.client', static function (Container $app) {
            return new Client\Minion(
                $app->make(LoopInterface::class), $app->make('minions.config')
            );
        });

        $this->app->singleton('minions.config', static function (Container $app) {
            return Configuration::make($app);
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

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['minions.client', 'minions.config'];
    }
}
