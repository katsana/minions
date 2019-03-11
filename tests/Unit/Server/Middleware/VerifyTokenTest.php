<?php

namespace Minions\Tests\Unit\Server\Middleware;

use Minions\Server\Middleware\VerifyToken;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class VerifyTokenTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_be_handled()
    {
        $middleware = new VerifyToken();

        $message = m::mock('Minions\Server\Message');

        $message->shouldReceive('validateRequestToken')->once()->andReturn(true);

        $middleware->handle($message, function ($passable) use ($message) {
            $this->assertSame($message, $passable);
        });
    }
}
