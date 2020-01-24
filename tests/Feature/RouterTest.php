<?php

namespace Minions\Tests\Feature;

use Minions\Router;
use Minions\Tests\HttpTestCase;

class RouterTest extends HttpTestCase
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
    public function it_can_resolve_the_facade()
    {
        $router = Router::getFacadeRoot();

        $this->assertInstanceOf('Minions\Http\Router', $router);
    }
}
