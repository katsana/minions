<?php

namespace Minions\Tests;

abstract class ServerTestCase extends TestCase
{
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
        $providers[] = 'Minions\Server\MinionsServiceProvider';
        $providers[] = 'Minions\Http\MinionsServiceProvider';

        return $providers;
    }
}
