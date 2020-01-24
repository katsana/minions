<?php

namespace Minions\Tests\Unit\Http;

use Carbon\Carbon;
use Minions\Http\Message;
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
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2]}');

        $message = new Message('foobar', [], $request);

        $this->assertSame($request, $message->request());
        $this->assertSame('{"jsonrpc":"2.0","method":"math/add","params":[1,2]}', $message->body());
        $this->assertSame('foobar', $message->id());
    }

    /** @test */
    public function it_can_validate_request_token()
    {
        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2]}')
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Authorization')->andReturn(['Token secret']);

        $message = new Message('foobar', ['token' => 'secret'], $request);

        $this->assertTrue($message->validateRequestToken());
    }

    /** @test */
    public function it_cant_validate_request_token_given_invalid_token()
    {
        $this->expectException('Minions\Exceptions\InvalidToken');

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2]}')
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Authorization')->andReturn(['Token secret!']);

        $message = new Message('foobar', ['token' => 'secret!!!'], $request);

        $message->validateRequestToken();
    }

    /** @test */
    public function it_cant_validate_request_token_when_token_is_missing()
    {
        $this->expectException('Minions\Exceptions\MissingToken');

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2]}')
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(false);

        $message = new Message('foobar', ['token' => 'secret'], $request);

        $message->validateRequestToken();
    }

    /** @test */
    public function it_cant_validate_request_token_when_token_is_not_configured()
    {
        $this->expectException('Minions\Exceptions\MissingToken');

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2]}')
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(['Token secret']);

        $message = new Message('foobar', ['token' => null], $request);

        $message->validateRequestToken();
    }

    /** @test */
    public function it_cant_validate_request_token_when_project_token_is_missing()
    {
        $this->expectException('Minions\Exceptions\MissingToken');

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2]}')
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true);

        $message = new Message('foobar', [], $request);

        $message->validateRequestToken();
    }

    /** @test */
    public function it_can_validate_request_signature()
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(1546300800));

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2]}')
            ->shouldReceive('hasHeader')->once()->with('X-Signature')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('X-Signature')->andReturn([
                't=1546300800,v1=3c1faf9b318b33b609f612e5e36cd5117fae4f4caf38c6141782b392ed3343d2',
            ]);

        $message = new Message('foobar', ['signature' => 'secret'], $request);

        $this->assertTrue($message->validateRequestSignature());
    }

    /** @test */
    public function it_cant_validate_request_signature_given_invalid_signature()
    {
        $this->expectException('Minions\Exceptions\InvalidSignature');

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2]}')
            ->shouldReceive('hasHeader')->once()->with('X-Signature')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('X-Signature')->andReturn([
                't=1546300800,v1=3c1faf9b318b33b609f612e5e36cd5117fae4f4caf38c6141782b392ed3343d2',
            ]);

        $message = new Message('foobar', ['signature' => 'secret!!!'], $request);

        $message->validateRequestSignature();
    }

    /** @test */
    public function it_cant_validate_request_signature_when_signature_is_missing()
    {
        $this->expectException('Minions\Exceptions\MissingSignature');

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2]}')
            ->shouldReceive('hasHeader')->once()->with('X-Signature')->andReturn(false);

        $message = new Message('foobar', ['signature' => 'secret'], $request);

        $message->validateRequestSignature();
    }

    /** @test */
    public function it_cant_validate_request_signature_when_signature_is_not_configured()
    {
        $this->expectException('Minions\Exceptions\MissingSignature');

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2]}')
            ->shouldReceive('hasHeader')->once()->with('X-Signature')->andReturn(true);

        $message = new Message('foobar', ['signature' => null], $request);

        $message->validateRequestSignature();
    }

    /** @test */
    public function it_cant_validate_request_signature_when_timestamp_has_expired()
    {
        $this->expectException('Minions\Exceptions\InvalidSignature');

        Carbon::setTestNow(Carbon::createFromTimestamp(1546301300));

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2]}')
            ->shouldReceive('hasHeader')->once()->with('X-Signature')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('X-Signature')->andReturn([
                't=1546300800,v1=3c1faf9b318b33b609f612e5e36cd5117fae4f4caf38c6141782b392ed3343d2',
            ]);

        $message = new Message('foobar', ['signature' => 'secret'], $request);

        $this->assertTrue($message->validateRequestSignature());
    }
}
