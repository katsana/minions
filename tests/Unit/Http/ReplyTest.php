<?php

namespace Minions\Tests\Unit\Http;

use Minions\Http\Reply;
use PHPUnit\Framework\TestCase;

class ReplyTest extends TestCase
{
    /** @test */
    public function it_can_be_initiated()
    {
        $reply = new Reply('{"jsonrpc":"2.0","id":1,"result":3}');

        $this->assertSame(200, $reply->status());
        $this->assertSame(['Content-Type' => 'application/json'], $reply->headers());
        $this->assertSame('{"jsonrpc":"2.0","id":1,"result":3}', $reply->body());
    }
}
