<?php

namespace Minions\Tests\Unit\Client;

use Clue\React\Buzz\Browser;
use Minions\Client\MessageInterface;
use Minions\Client\Project;
use Minions\Client\Response;
use Minions\Client\ResponseInterface;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as ResponseContract;

class ProjectTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_broadcast_message()
    {
        $browser = m::mock(Browser::class);
        $message = m::mock(MessageInterface::class);

        $browser->shouldReceive('post')->with('/', [
            'Content-Type' => 'application/json',
            'X-Request-ID' => 'platform',
            'Authorization' => 'Token secret',
            'HTTP_X_SIGNATURE' => 't=1546300800,v1=3c1faf9b318b33b609f612e5e36cd5117fae4f4caf38c6141782b392ed3343d2',
        ], '{"jsonrpc":"2.0","method":"math/add","params":[1,2]}')->andReturnSelf();
        $browser->shouldReceive('then')->andReturnUsing(function ($r) use ($message) {
            $response = m::mock(ResponseContract::class);
            $response->shouldReceive('getStatusCode')->andReturn(204)
                ->shouldReceive('getBody')->andReturn('{"jsonrpc":"2.0","result":3}');

            return (new Response($response))->validate($message);
        });

        $message->shouldReceive('signature')
            ->with('secret')->andReturn('t=1546300800,v1=3c1faf9b318b33b609f612e5e36cd5117fae4f4caf38c6141782b392ed3343d2');
        $message->shouldReceive('toJson')->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2]}');

        $project = new Project('platform', ['token' => 'secret', 'signature' => 'secret'], $browser);

        $this->assertInstanceOf(ResponseInterface::class, $project->broadcast($message));
    }
}
