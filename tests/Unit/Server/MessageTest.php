<?php

namespace Minions\Tests\Unit\Server;

use Minions\Server\Message;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_be_initiated()
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getBody')->once()->andReturn('Hello world');

        $message = new Message('foobar', [], $request);

        $this->assertSame($request, $message->request());
        $this->assertSame('Hello world', $message->body());
    }
}
