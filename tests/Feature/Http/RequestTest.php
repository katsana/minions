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
    public function it_can_handle_a_handler()
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
        ], $request->handle(RequestHandler::class));
    }

    /** @test */
    public function it_cant_handle_uncallable_handler()
    {
        $this->expectException('Minions\Exceptions\Exception');
        $this->expectExceptionMessage('Method not found');

        $message = new Message('platform', [], $this->mockServerRequestInterface());

        $request = new Request([
            'id' => 5,
            'email' => 'crynobone@katsana.com',
            'name' => 'Mior Muhammad Zaki',
        ], $message);

        $request->handle(InvalidRequestHandler::class);
    }

    /** @test */
    public function it_cant_handle_anything_other_than_a_valid_handler()
    {
        $this->expectException('Minions\Exceptions\Exception');
        $this->expectExceptionMessage('Method not found');

        $message = new Message('platform', [], $this->mockServerRequestInterface());

        $request = new Request([
            'id' => 5,
            'email' => 'crynobone@katsana.com',
            'name' => 'Mior Muhammad Zaki',
        ], $message);

        $request->handle(null);
    }

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
        ], $request->forwardCallTo(RequestHandler::class));
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
        ], $request->forwardCallTo(RequestHandler::class, [
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
        ], $request->forwardCallTo(new RequestHandler()));
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

class RequestHandler
{
    public function __invoke(Request $request)
    {
        return $request->all();
    }
}

class InvalidRequestHandler
{
    //
}
