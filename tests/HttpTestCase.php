<?php

namespace Minions\Tests;

abstract class HttpTestCase extends TestCase
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
        $providers[] = 'Minions\Http\MinionsServiceProvider';

        return $providers;
    }
}
