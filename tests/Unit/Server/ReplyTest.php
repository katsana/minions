<?php

namespace Minions\Tests\Unit\Server;

use Minions\Server\Reply;
use PHPUnit\Framework\TestCase;

class ReplyTest extends TestCase
{
    /** @test */
    public function it_can_be_initiated()
    {
        $reply = new Reply("hello world");

        $this->assertSame(200, $reply->status());
        $this->assertSame(['Content-Type' => 'application/json'], $reply->headers());
        $this->assertSame("hello world", $reply->body());
    }

     /** @test */
    public function it_can_be_converted_to_psr7_response()
    {
        $reply = new Reply("hello world");
        $response = $reply->asResponse();

        $this->assertInstanceOf('React\Http\Response', $response);
        $this->assertInstanceOf('RingCentral\Psr7\Response', $response);
    }
}
