<?php

namespace Minions\Tests\Feature\Http;

use Carbon\Carbon;
use Minions\Http\Router;
use Minions\Tests\HttpTestCase;
use Mockery as m;

class RouterTest extends HttpTestCase
{
    /** @test */
    public function it_can_dispatch_the_request()
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(1546300800));

        config(['minions' => [
            'id' => 'foobar',
            'projects' => [
                'demo' => [
                    'token' => 'secret!',
                    'signature' => 'secret',
                ],
            ],
            'services' => [
                'math/add' => 'Minions\Tests\Stubs\MathAdd',
            ],
        ]]);

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getHeader')->once()->with('X-Request-ID')->andReturn(['demo'])
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Authorization')->andreturn(['Token secret!'])
            ->shouldReceive('hasHeader')->once()->with('X-Signature')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('X-Signature')->andReturn([
                't=1546300800,v1=17f40ac0151055bd2c464a58eafdff28bf25d3118d41005f8209754b26f0e20a',
            ])
            ->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2],"id":3}');

        $reply = $this->app['minions.router']->handle($request);

        $this->assertInstanceOf('Minions\Http\Reply', $reply);
        $this->assertSame('{"jsonrpc":"2.0","id":3,"result":3}', $reply->body());
    }

    /** @test */
    public function it_can_dispatch_the_request_with_fluent_router()
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(1546300800));

        config(['minions' => [
            'id' => 'foobar',
            'projects' => [
                'demo' => [
                    'token' => 'secret!',
                    'signature' => 'secret',
                ],
            ],
        ]]);

        $router = $this->app['minions.router'];

        $router->rpc('math/add', 'Minions\Tests\Stubs\MathAdd');

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getHeader')->once()->with('X-Request-ID')->andReturn(['demo'])
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Authorization')->andreturn(['Token secret!'])
            ->shouldReceive('hasHeader')->once()->with('X-Signature')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('X-Signature')->andReturn([
                't=1546300800,v1=17f40ac0151055bd2c464a58eafdff28bf25d3118d41005f8209754b26f0e20a',
            ])
            ->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2],"id":3}');

        $reply = $router->handle($request);

        $this->assertInstanceOf('Minions\Http\Reply', $reply);
        $this->assertSame('{"jsonrpc":"2.0","id":3,"result":3}', $reply->body());
    }

    /** @test */
    public function it_can_dispatch_the_request_with_custom_router_resolver()
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(1546300800));

        config(['minions' => [
            'id' => 'foobar',
            'projects' => [
                'demo' => [
                    'token' => 'secret!',
                    'signature' => 'secret',
                ],
            ],
        ]]);

        $router = $this->app['minions.router'];

        Router::routeResolver(static function () use ($router) {
            $router->rpc('math/add', 'Minions\Tests\Stubs\MathAdd');
        });

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getHeader')->once()->with('X-Request-ID')->andReturn(['demo'])
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Authorization')->andreturn(['Token secret!'])
            ->shouldReceive('hasHeader')->once()->with('X-Signature')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('X-Signature')->andReturn([
                't=1546300800,v1=17f40ac0151055bd2c464a58eafdff28bf25d3118d41005f8209754b26f0e20a',
            ])
            ->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2],"id":3}');

        $reply = $router->handle($request);

        $this->assertInstanceOf('Minions\Http\Reply', $reply);
        $this->assertSame('{"jsonrpc":"2.0","id":3,"result":3}', $reply->body());
    }

    /** @test */
    public function it_can_dispatch_the_request_when_all_project_is_authorized()
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(1546300800));

        config(['minions' => [
            'id' => 'foobar',
            'projects' => [
                'demo' => [
                    'token' => 'secret!',
                    'signature' => 'secret',
                ],
            ],
            'services' => [
                'math/add' => ['handler' => 'Minions\Tests\Stubs\MathAdd', 'projects' => ['*']],
            ],
        ]]);

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getHeader')->once()->with('X-Request-ID')->andReturn(['demo'])
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Authorization')->andreturn(['Token secret!'])
            ->shouldReceive('hasHeader')->once()->with('X-Signature')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('X-Signature')->andReturn([
                't=1546300800,v1=17f40ac0151055bd2c464a58eafdff28bf25d3118d41005f8209754b26f0e20a',
            ])
            ->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2],"id":3}');

        $reply = $this->app['minions.router']->handle($request);

        $this->assertInstanceOf('Minions\Http\Reply', $reply);
        $this->assertSame('{"jsonrpc":"2.0","id":3,"result":3}', $reply->body());
    }

    /** @test */
    public function it_can_dispatch_the_request_when_project_is_not_declared()
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(1546300800));

        config(['minions' => [
            'id' => 'foobar',
            'projects' => [
                'demo' => [
                    'token' => 'secret!',
                    'signature' => 'secret',
                ],
            ],
            'services' => [
                'math/add' => ['handler' => 'Minions\Tests\Stubs\MathAdd'],
            ],
        ]]);

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getHeader')->once()->with('X-Request-ID')->andReturn(['demo'])
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Authorization')->andreturn(['Token secret!'])
            ->shouldReceive('hasHeader')->once()->with('X-Signature')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('X-Signature')->andReturn([
                't=1546300800,v1=17f40ac0151055bd2c464a58eafdff28bf25d3118d41005f8209754b26f0e20a',
            ])
            ->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2],"id":3}');

        $reply = $this->app['minions.router']->handle($request);

        $this->assertInstanceOf('Minions\Http\Reply', $reply);
        $this->assertSame('{"jsonrpc":"2.0","id":3,"result":3}', $reply->body());
    }

    /** @test */
    public function it_can_dispatch_the_request_when_current_project_is_authorized()
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(1546300800));

        config(['minions' => [
            'id' => 'foobar',
            'projects' => [
                'demo' => [
                    'token' => 'secret!',
                    'signature' => 'secret',
                ],
            ],
            'services' => [
                'math/add' => ['handler' => 'Minions\Tests\Stubs\MathAdd', 'projects' => ['demo']],
            ],
        ]]);

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getHeader')->once()->with('X-Request-ID')->andReturn(['demo'])
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Authorization')->andreturn(['Token secret!'])
            ->shouldReceive('hasHeader')->once()->with('X-Signature')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('X-Signature')->andReturn([
                't=1546300800,v1=17f40ac0151055bd2c464a58eafdff28bf25d3118d41005f8209754b26f0e20a',
            ])
            ->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2],"id":3}');

        $reply = $this->app['minions.router']->handle($request);

        $this->assertInstanceOf('Minions\Http\Reply', $reply);
        $this->assertSame('{"jsonrpc":"2.0","id":3,"result":3}', $reply->body());
    }

    /** @test */
    public function it_cant_dispatch_the_request_when_service_cant_be_evaluated()
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(1546300800));

        config(['minions' => [
            'id' => 'foobar',
            'projects' => [
                'demo' => [
                    'token' => 'secret!',
                    'signature' => 'secret',
                ],
            ],
            'services' => [
                'math/deduct' => 'Minions\Tests\Stubs\MathAdd',
            ],
        ]]);

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getHeader')->once()->with('X-Request-ID')->andReturn(['demo'])
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Authorization')->andreturn(['Token secret!'])
            ->shouldReceive('hasHeader')->once()->with('X-Signature')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('X-Signature')->andReturn([
                't=1546300800,v1=17f40ac0151055bd2c464a58eafdff28bf25d3118d41005f8209754b26f0e20a',
            ])
            ->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2],"id":3}');

        $reply = $this->app['minions.router']->handle($request);

        $this->assertInstanceOf('Minions\Http\Reply', $reply);
        $this->assertSame('{"jsonrpc":"2.0","id":3,"error":{"code":-32601,"message":"Method not found"}}', $reply->body());
    }

    /** @test */
    public function it_cant_dispatch_the_request_when_current_project_not_authorized()
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(1546300800));

        config(['minions' => [
            'id' => 'foobar',
            'projects' => [
                'demo' => [
                    'token' => 'secret!',
                    'signature' => 'secret',
                ],
            ],
            'services' => [
                'math/add' => ['handler' => 'Minions\Tests\Stubs\MathAdd', 'projects' => []],
            ],
        ]]);

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getHeader')->once()->with('X-Request-ID')->andReturn(['demo'])
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Authorization')->andreturn(['Token secret!'])
            ->shouldReceive('hasHeader')->once()->with('X-Signature')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('X-Signature')->andReturn([
                't=1546300800,v1=17f40ac0151055bd2c464a58eafdff28bf25d3118d41005f8209754b26f0e20a',
            ])
            ->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2],"id":3}');

        $reply = $this->app['minions.router']->handle($request);

        $this->assertInstanceOf('Minions\Http\Reply', $reply);
        $this->assertSame('{"jsonrpc":"2.0","id":3,"error":{"code":-32601,"message":"Method not found"}}', $reply->body());
    }

    /** @test */
    public function it_cant_dispatch_the_request_when_service_cant_be_resolved()
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(1546300800));

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
            ->shouldReceive('hasHeader')->once()->with('X-Signature')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('X-Signature')->andReturn([
                't=1546300800,v1=17f40ac0151055bd2c464a58eafdff28bf25d3118d41005f8209754b26f0e20a',
            ])
            ->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2],"id":3}');

        $reply = $this->app['minions.router']->handle($request);

        $this->assertInstanceOf('Minions\Http\Reply', $reply);
        $this->assertSame('{"jsonrpc":"2.0","id":3,"error":{"code":-32601,"message":"Method not found"}}', $reply->body());
    }

    /** @test */
    public function it_cant_dispatch_the_request_is_not_authorized()
    {
        Carbon::setTestNow(Carbon::createFromTimestamp(1546300800));

        config(['minions' => [
            'id' => 'foobar',
            'projects' => [
                'demo' => [
                    'token' => 'secret!',
                    'signature' => 'secret',
                ],
                'dummy' => [
                    'token' => 'secret!',
                    'signature' => 'secret',
                ],
            ],
            'services' => [
                'math/substract' => 'Minions\Tests\Stubs\MathSubstract',
            ],
        ]]);

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getHeader')->once()->with('X-Request-ID')->andReturn(['dummy'])
            ->shouldReceive('hasHeader')->once()->with('Authorization')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('Authorization')->andreturn(['Token secret!'])
            ->shouldReceive('hasHeader')->once()->with('X-Signature')->andReturn(true)
            ->shouldReceive('getHeader')->once()->with('X-Signature')->andReturn([
                't=1546300800,v1=17f40ac0151055bd2c464a58eafdff28bf25d3118d41005f8209754b26f0e20a',
            ])
            ->shouldReceive('getBody')->once()->andReturn('{"jsonrpc":"2.0","method":"math/add","params":[1,2],"id":3}');

        $reply = $this->app['minions.router']->handle($request);

        $this->assertInstanceOf('Minions\Http\Reply', $reply);
        $this->assertSame('{"jsonrpc":"2.0","id":3,"error":{"code":-32601,"message":"Method not found"}}', $reply->body());
    }

    /** @test */
    public function it_fails_when_project_id_is_missing()
    {
        config(['minions' => ['id' => 'foobar', 'projects' => []]]);

        $request = m::mock('Psr\Http\Message\ServerRequestInterface');

        $request->shouldReceive('getHeader')->once()->with('X-Request-ID')->andReturn(['hello']);

        $reply = $this->app['minions.router']->handle($request);

        $this->assertInstanceOf('Minions\Http\Reply', $reply);
        $this->assertSame('{"jsonrpc":"2.0","id":null,"error":{"code":-32600,"message":"Unable to find project: hello"}}', $reply->body());
    }
}
