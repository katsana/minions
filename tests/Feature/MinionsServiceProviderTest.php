<?php

namespace Minions\Tests\Feature;

use Minions\Tests\ServerTestCase;

class MinionsServiceProviderTest extends ServerTestCase
{
    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        config([
            'minions' => ['id' => 'minions', 'projects' => []],
        ]);
    }

    /** @test */
    public function it_register_the_services()
    {
        $this->assertInstanceOf('Minions\Client\Minion', $this->app['minions.client']);
    }
}
