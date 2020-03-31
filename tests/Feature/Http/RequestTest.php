<?php

namespace Minions\Tests\Feature\Http;

use Minions\Http\Message;
use Minions\Http\Request;
use Minions\Tests\TestCase;
use Mockery as m;
use Psr\Http\Message\ServerRequestInterface;

class RequestTest extends TestCase
{
    /** @test */
    public function it_can_forward_to_another_handler()
    {
        $message = new Message('platform', [], $this->mockServerRequestInterface());

        $request = new Request([
            'id' => 5,
            'email' => 'crynobone@katsana.com',
            'name' => 'Mior Muhammad Zaki',
        ], $message);

        $this->assertSame([
            'id' => 5,
            'email' => 'crynobone@katsana.com',
            'name' => 'Mior Muhammad Zaki',
        ], $request->forwardCallTo(InternalRequestHandler::class));
    }

    /** @test */
    public function it_can_forward_to_another_handler_with_custom_parameters()
    {
        $message = new Message('platform', [], $this->mockServerRequestInterface());

        $request = new Request([
            'id' => 5,
            'email' => 'crynobone@katsana.com',
            'name' => 'Mior Muhammad Zaki',
        ], $message);

        $this->assertSame([
            'email' => 'crynobone@gmail.com',
        ], $request->forwardCallTo(InternalRequestHandler::class, [
            'email' => 'crynobone@gmail.com',
        ]));
    }

    /** @test */
    public function it_can_forward_to_another_handler_from_an_object()
    {
        $message = new Message('platform', [], $this->mockServerRequestInterface());

        $request = new Request([
            'id' => 5,
            'email' => 'crynobone@katsana.com',
            'name' => 'Mior Muhammad Zaki',
        ], $message);

        $this->assertSame([
            'id' => 5,
            'email' => 'crynobone@katsana.com',
            'name' => 'Mior Muhammad Zaki',
        ], $request->forwardCallTo(new InternalRequestHandler()));
    }

    /**
     * Get `Psr\Http\Message\ServerRequestInterface` mocked instance.
     */
    protected function mockServerRequestInterface()
    {
        return \tap(m::mock(ServerRequestInterface::class), static function ($server) {
            $server->shouldReceive('getBody')->andReturn('');
        });
    }
}

class InternalRequestHandler
{
    public function __invoke(Request $request)
    {
        return $request->all();
    }
}
