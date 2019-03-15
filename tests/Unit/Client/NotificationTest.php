<?php

namespace Minions\Tests\Unit\Client;

use Carbon\Carbon;
use Minions\Client\Notification;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        Carbon::setTestNow(null);
        m::close();
    }

    /** @test */
    public function it_can_be_initiated()
    {
        $message = new Notification('math/add', [1, 2]);

        $this->assertSame('2.0', $message->version());
        $this->assertSame('math/add', $message->method());
        $this->assertSame([1, 2], $message->parameters());
    }

    /** @test */
    public function it_can_be_converted_to_json()
    {
        $message = new Notification('math/add', [1, 2]);

        $this->assertSame(
            '{"jsonrpc":"2.0","method":"math\/add","params":[1,2]}', $message->toJson()
        );
    }

    /** @test */
    public function it_can_generate_correct_signature()
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(1546300800));
        $message = new Notification('math/add', [1, 2]);
        // '{"jsonrpc":"2.0","method":"math/add","params":[1,2]}'
        $this->assertSame(
            't=1546300800,v1=3c1faf9b318b33b609f612e5e36cd5117fae4f4caf38c6141782b392ed3343d2', $message->signature('secret')
        );

        Carbon::setTestNow(Carbon::createFromTimestamp(1546300802));

        $this->assertSame(
            't=1546300802,v1=fb0c53f47a4664d5f45ea0ac32d5c8f6af20d82e1546aa6caae0f7e341c4e998', $message->signature('secret')
        );
    }
}
