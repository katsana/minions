<?php

namespace Minions\Tests\Feature\Server;

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
            'minions' => ['id' => 'platform', 'services' => []],
        ]);
    }

    /** @test */
    public function it_register_the_services()
    {
        $this->assertInstanceOf('Minions\Server\Evaluator', $this->app['minions.evaluator']);
        $this->assertInstanceOf('Minions\Server\Request', $this->app['minions.request']);
    }
}
