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
            'minions' => ['id' => 'minions', 'projects' => []],
        ]);
    }

    /** @test */
    public function it_can_resolve_the_facade()
    {
        $minion = Minion::getFacadeRoot();

        $this->assertInstanceOf('Minions\Client\Minion', $minion);
    }

    /** @test */
    public function it_can_generate_a_message()
    {
        $message = Minion::message('math/add', [1, 2], 1);

        $this->assertInstanceOf('Minions\Client\Message', $message);
        $this->assertSame('2.0', $message->version());
        $this->assertSame('math/add', $message->method());
        $this->assertSame([1, 2], $message->parameters());
        $this->assertSame(1, $message->id());
    }

     /** @test */
    public function it_can_generate_a_message_can_generate_uniqid()
    {
        $message = Minion::message('math/add', [1, 2]);

        $this->assertInstanceOf('Minions\Client\Message', $message);
        $this->assertSame('2.0', $message->version());
        $this->assertSame('math/add', $message->method());
        $this->assertSame([1, 2], $message->parameters());
        $this->assertNotNull($message->id());
    }

    /** @test */
    public function it_can_generate_a_notification()
    {
        $message = Minion::notification('math/add', [1, 2]);

        $this->assertInstanceOf('Minions\Client\Notification', $message);
        $this->assertSame('2.0', $message->version());
        $this->assertSame('math/add', $message->method());
        $this->assertSame([1, 2], $message->parameters());
    }
}
