<?php

namespace Minions\Tests\Feature;

use Minions\Minion;
use Minions\Tests\TestCase;

class MinionTest extends TestCase
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
            'minions' => ['id' => 'platform', 'projects' => []],
        ]);
    }

    /** @test */
    public function it_can_resolve_the_facade()
    {
        $minion = Minion::getFacadeRoot();

        $this->assertInstanceOf('Minions\Client\Minion', $minion);
    }
}
