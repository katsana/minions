<?php

namespace Minions\Tests\Unit\Client;

use Minions\Client\MessageInterface;
use Minions\Client\Response;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as ResponseContract;

class ResponseTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_received_successful_response_via_200_status_code()
    {
        $psr7Response = m::mock(ResponseContract::class);

        $psr7Response->shouldReceive('getStatusCode')->andReturn(200)
            ->shouldReceive('getBody')->andReturn('{"jsonrpc":"2.0","id":1,"result":3}');

        $response = new Response($psr7Response);

        $this->assertSame(1, $response->getRpcId());
        $this->assertSame(3, $response->getRpcResult());
        $this->assertSame('2.0', $response->getRpcVersion());
        $this->assertNull($response->getRpcErrorCode());
        $this->assertNull($response->getRpcErrorMessage());
        $this->assertNull($response->getRpcErrorData());
        $this->assertSame([
            'jsonrpc' => '2.0',
            'id' => 1,
            'result' => 3,
        ], $response->toArray());
    }

    /** @test */
    public function it_can_received_successful_response_201_status_code()
    {
        $psr7Response = m::mock(ResponseContract::class);

        $psr7Response->shouldReceive('getStatusCode')->andReturn(201)
            ->shouldReceive('getBody')->andReturn('{"jsonrpc":"2.0","id":1,"result":3}');

        $response = new Response($psr7Response);

        $this->assertSame(1, $response->getRpcId());
        $this->assertSame(3, $response->getRpcResult());
        $this->assertSame('2.0', $response->getRpcVersion());
        $this->assertNull($response->getRpcErrorCode());
        $this->assertNull($response->getRpcErrorMessage());
        $this->assertNull($response->getRpcErrorData());
    }

    /** @test */
    public function it_can_received_successful_response_204_status_code()
    {
        $psr7Response = m::mock(ResponseContract::class);

        $psr7Response->shouldReceive('getStatusCode')->andReturn(204)
            ->shouldReceive('getBody')->never();

        $response = new Response($psr7Response);

        $this->assertNull($response->getRpcId());
        $this->assertNull($response->getRpcResult());
        $this->assertSame('2.0', $response->getRpcVersion());
        $this->assertNull($response->getRpcErrorCode());
        $this->assertNull($response->getRpcErrorMessage());
        $this->assertNull($response->getRpcErrorData());
    }

    /** @test */
    public function it_can_received_failed_response_with_other_status_code()
    {
        $psr7Response = m::mock(ResponseContract::class);

        $psr7Response->shouldReceive('getStatusCode')->andReturn(410)
            ->shouldReceive('getReasonPhrase')->once()->andReturn('Gone')
            ->shouldReceive('getBody')->once()->andReturn('Response gone!');

        $response = new Response($psr7Response);

        $this->assertNull($response->getRpcId());
        $this->assertNull($response->getRpcResult());
        $this->assertSame('2.0', $response->getRpcVersion());
        $this->assertSame(-32600, $response->getRpcErrorCode());
        $this->assertSame('Gone', $response->getRpcErrorMessage());
        $this->assertSame([
            'status' => 410,
            'body' => 'Response gone!',
        ], $response->getRpcErrorData());
    }

    /** @test */
    public function it_can_received_failed_response()
    {
        $psr7Response = m::mock(ResponseContract::class);

        $psr7Response->shouldReceive('getStatusCode')->andReturn(200)
            ->shouldReceive('getBody')->andReturn('{"jsonrpc":"2.0","id":1,"error":{"code":-32600,"message":"Unable to find project: foobar"}}');

        $response = new Response($psr7Response);

        $this->assertSame(1, $response->getRpcId());
        $this->assertNull($response->getRpcResult());
        $this->assertSame('2.0', $response->getRpcVersion());
        $this->assertSame(-32600, $response->getRpcErrorCode());
        $this->assertSame('Unable to find project: foobar', $response->getRpcErrorMessage());
        $this->assertNull($response->getRpcErrorData());
    }

    /** @test */
    public function it_can_throw_client_has_error_exception()
    {
        $this->expectException('Minions\Exceptions\ClientHasError');
        $this->expectExceptionMessage('Unable to find project: foobar');

        $psr7Response = m::mock(ResponseContract::class);
        $message = m::mock(MessageInterface::class);

        $message->shouldReceive('method')->andReturn('math.add');

        $psr7Response->shouldReceive('getStatusCode')->andReturn(200)
            ->shouldReceive('getBody')->andReturn('{"jsonrpc":"2.0","id":1,"error":{"code":-32600,"message":"Unable to find project: foobar"}}');

        $response = (new Response($psr7Response))->validate($message);
    }

    /** @test */
    public function it_can_throw_server_has_error_exception()
    {
        $this->expectException('Minions\Exceptions\ServerHasError');
        $this->expectExceptionMessage('Missing Signature.');

        $psr7Response = m::mock(ResponseContract::class);
        $message = m::mock(MessageInterface::class);

        $message->shouldReceive('method')->andReturn('math.add');

        $psr7Response->shouldReceive('getStatusCode')->andReturn(200)
            ->shouldReceive('getBody')->andReturn('{"jsonrpc":"2.0","id":1,"error":{"code":-32651,"message":"Missing Signature."}}');

        $response = (new Response($psr7Response))->validate($message);
    }

    /** @test */
    public function it_can_serialize_the_response()
    {
        $psr7Response = m::mock(ResponseContract::class);

        $psr7Response->shouldReceive('getStatusCode')->andReturn(200)
            ->shouldReceive('getBody')->andReturn('{"jsonrpc":"2.0","id":1,"error":{"code":-32651,"message":"Missing Signature."}}');

        $response = (new Response($psr7Response));

        $this->assertSame('C:23:"Minions\Client\Response":141:{a:1:{s:7:"content";a:3:{s:7:"jsonrpc";s:3:"2.0";s:2:"id";i:1;s:5:"error";a:2:{s:4:"code";i:-32651;s:7:"message";s:18:"Missing Signature.";}}}}', \serialize($response));
    }

    /** @test */
    public function it_can_unserialize_the_response()
    {
        $psr7Response = m::mock(ResponseContract::class);

        $psr7Response->shouldReceive('getStatusCode')->andReturn(200)
            ->shouldReceive('getBody')->andReturn('{"jsonrpc":"2.0","id":1,"error":{"code":-32651,"message":"Missing Signature."}}');

        $response = \unserialize('C:23:"Minions\Client\Response":141:{a:1:{s:7:"content";a:3:{s:7:"jsonrpc";s:3:"2.0";s:2:"id";i:1;s:5:"error";a:2:{s:4:"code";i:-32651;s:7:"message";s:18:"Missing Signature.";}}}}');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(-32651, $response->getRpcErrorCode());
        $this->assertSame('Missing Signature.', $response->getRpcErrorMessage());
        $this->assertNull($response->getRpcErrorData());
    }
}
