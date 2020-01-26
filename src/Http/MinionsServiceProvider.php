<?php

namespace Minions\Http;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Minions\Concerns\Configuration;
use Orchestra\Canvas\Core\CommandsProvider;

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
        $this->bootRpcRoutes();

        $this->publishes([
            __DIR__.'/stubs/route.stub' => \base_path('routes/rpc.php'),
        ], 'routes');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\MakeRpcRequest::class,
            ]);
        }
    }

    /**
     * Register rpc routes.
     *
     * @return void
     */
    protected function bootRpcRoutes()
    {
        $routeFile = $this->app->basePath('routes/rpc.php');

        if (\file_exists($routeFile)) {
            Router::routeResolver(static function () use ($routeFile) {
                require $routeFile;
            });
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
