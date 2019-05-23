<?php

namespace Minions\Tests\Feature;

use Minions\Router;
use Minions\Tests\ServerTestCase;

class RouterTest extends ServerTestCase
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
            'minions' => ['id' => 'platform'],
        ]);
    }

    /** @test */
    public function it_can_resolve_the_facade()
    {
        $router = Router::getFacadeRoot();

        $this->assertInstanceOf('Minions\Server\Router', $router);
    }
}
