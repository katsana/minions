<?php

namespace Minions\Tests\Unit\Client;

use Carbon\Carbon;
use Minions\Client\Message;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
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
        $message = new Message('math/add', [1, 2], 1);

        $this->assertSame('2.0', $message->version());
        $this->assertSame('math/add', $message->method());
        $this->assertSame([1, 2], $message->parameters());
        $this->assertSame(1, $message->id());
    }

    /** @test */
    public function it_can_be_converted_to_json()
    {
        $message = new Message('math/add', [1, 2], 2);

        $this->assertSame(
            '{"jsonrpc":"2.0","method":"math\/add","params":[1,2],"id":2}', $message->toJson()
        );
    }

    /** @test */
    public function it_can_generate_correct_signature()
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(1546300800));
        $message = new Message('math/add', [1, 2], 3);

        $this->assertSame(
            't=1546300800,v1=17f40ac0151055bd2c464a58eafdff28bf25d3118d41005f8209754b26f0e20a', $message->signature('secret')
        );

        Carbon::setTestNow(Carbon::createFromTimestamp(1546300802));

        $this->assertSame(
            't=1546300802,v1=cb346293fa89b95ae84ba87a2e11d515c14c61425ac045dff68fde598823c490', $message->signature('secret')
        );
    }

    /** @test */
    public function it_can_be_transform_to_request()
    {
        $client = m::mock('Graze\GuzzleHttp\JsonRpc\ClientInterface');

        $client->shouldReceive('request')->with(4, 'math/add', [1, 2])->andReturnSelf();

        $message = new Message('math/add', [1, 2], 4);

        $this->assertEquals($client, $message->asRequest($client));
    }
}
