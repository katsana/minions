<?php

namespace Minions\Tests\Feature\Server;

use Minions\Tests\ServerTestCase;
use Mockery as m;

class RequestTest extends ServerTestCase
{
    /** @test */
    public function it_can_dispatch_the_request()
    {
        config(['minions' => [
            'id' => 'foobar',
            'projects' => [
                'demo' => [
                    'token' => 'secret!',
                    'signature' => 'secret',
                ],
            ],
            'services' => [
                'math/add' => 'Minions\Tests\Stubs\AddMath',
            ],
        ]]);

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getHeader')->once()->with('X-Request-ID')->andReturn(['demo'])
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Authorization')->andreturn(['Token secret!'])
            ->shouldReceive('hasHeader')->once()->with('HTTP_X_SIGNATURE')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('HTTP_X_SIGNATURE')->andReturn([
                't=1546300800,v1=17f40ac0151055bd2c464a58eafdff28bf25d3118d41005f8209754b26f0e20a',
            ])
            ->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2],"id":3}');

        $reply = $this->app['minions.request']->handle($request);

        $this->assertInstanceOf('Minions\Server\Reply', $reply);
        $this->assertSame('{"jsonrpc":"2.0","id":3,"result":3}', $reply->body());
    }

    /** @test */
    public function it_cant_dispatch_the_request_when_service_cant_be_evaluated()
    {
        config(['minions' => [
            'id' => 'foobar',
            'projects' => [
                'demo' => [
                    'token' => 'secret!',
                    'signature' => 'secret',
                ],
            ],
            'services' => [
                'math/deduct' => 'Minions\Tests\Stubs\AddMath',
            ],
        ]]);

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getHeader')->once()->with('X-Request-ID')->andReturn(['demo'])
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Authorization')->andreturn(['Token secret!'])
            ->shouldReceive('hasHeader')->once()->with('HTTP_X_SIGNATURE')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('HTTP_X_SIGNATURE')->andReturn([
                't=1546300800,v1=17f40ac0151055bd2c464a58eafdff28bf25d3118d41005f8209754b26f0e20a',
            ])
            ->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2],"id":3}');

        $reply = $this->app['minions.request']->handle($request);

        $this->assertInstanceOf('Minions\Server\Reply', $reply);
        $this->assertSame('{"jsonrpc":"2.0","id":3,"error":{"code":-32601,"message":"Method not found"}}', $reply->body());
    }

    /** @test */
    public function it_cant_dispatch_the_request_when_service_cant_be_resolved()
    {
        config(['minions' => [
            'id' => 'foobar',
            'projects' => [
                'demo' => [
                    'token' => 'secret!',
                    'signature' => 'secret',
                ],
            ],
            'services' => [
                'math/add' => 'add-math-stub',
            ],
        ]]);

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getHeader')->once()->with('X-Request-ID')->andReturn(['demo'])
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Authorization')->andreturn(['Token secret!'])
            ->shouldReceive('hasHeader')->once()->with('HTTP_X_SIGNATURE')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('HTTP_X_SIGNATURE')->andReturn([
                't=1546300800,v1=17f40ac0151055bd2c464a58eafdff28bf25d3118d41005f8209754b26f0e20a',
            ])
            ->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2],"id":3}');

        $reply = $this->app['minions.request']->handle($request);

        $this->assertInstanceOf('Minions\Server\Reply', $reply);
        $this->assertSame('{"jsonrpc":"2.0","id":3,"error":{"code":-32601,"message":"Method not found"}}', $reply->body());
    }

    /** @test */
    public function it_fails_when_project_id_is_missing()
    {
        config(['minions' => ['id' => 'foobar', 'projects' => []]]);

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getHeader')->once()->with('X-Request-ID')->andReturn(['hello']);

        $reply = $this->app['minions.request']->handle($request);

        $this->assertInstanceOf('Minions\Server\Reply', $reply);
        $this->assertSame('{"jsonrpc":"2.0","id":null,"error":{"code":-32600,"message":"Unable to find project: hello"}}', $reply->body());
    }
}
