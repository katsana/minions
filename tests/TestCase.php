<?php

namespace Minions\Tests;

use Orchestra\Testbench\TestCase as Testing;

abstract class TestCase extends Testing
{
    /**
     * Get package aliases.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Minions' => 'Minions\Minion',
        ];
    }

    /**
     * Get application providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getApplicationProviders($app)
    {
        $providers = parent::getApplicationProviders($app);
        $providers[] = 'Laravie\Stream\Laravel\StreamServiceProvider';

        return $providers;
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Minions\MinionsServiceProvider',
        ];
    }
}
