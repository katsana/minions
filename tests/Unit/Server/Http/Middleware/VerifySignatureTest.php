<?php

namespace Minions\Tests\Unit\Http\Middleware;

use Minions\Http\Middleware\VerifySignature;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class VerifySignatureTest extends TestCase
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
        $middleware = new VerifySignature();

        $message = m::mock('Minions\Http\Message');

        $message->shouldReceive('validateRequestSignature')->once()->andReturn(true);

        $middleware->handle($message, function ($passable) use ($message) {
            $this->assertSame($message, $passable);
        });
    }
}
