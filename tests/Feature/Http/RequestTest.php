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
    public function it_can_handle_a_handler_with_middleware()
    {
        unset($_SERVER['Request-ID']);

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
        ], $request->handle(RequestHandlerWithMiddleware::class));

        $this->assertSame('platform', $_SERVER['Request-ID']);

        unset($_SERVER['Request-ID']);
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

class RequestHandlerWithMiddleware
{
    /**
     * Get the middleware the request should pass through.
     *
     * @return array
     */
    public function middleware(): array
    {
        return [
            new AddMessageIdToSession(),
        ];
    }

    public function __invoke(Request $request)
    {
        return $request->all();
    }
}

class InvalidRequestHandler
{
    //
}

class AddMessageIdToSession
{
    public function handle(Request $request, \Closure $next)
    {
        $_SERVER['Request-ID'] = $request->id();

        return $next($request);
    }
}
