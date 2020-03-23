<?php

namespace Minions\Tests\Unit\Http;

use Minions\Http\Message;
use Minions\Http\Request;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RequestTest extends TestCase
{
    /** @test */
    public function it_has_proper_signature()
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
        ], $request->all());

        $this->assertSame([
            'id' => 5,
            'email' => 'crynobone@katsana.com',
            'name' => 'Mior Muhammad Zaki',
        ], $request->toArray());

        $this->assertSame('platform', $request->id());
    }

    /** @test */
    public function it_has_access_to_array()
    {
        $message = new Message('platform', [], $this->mockServerRequestInterface());

        $request = new Request([
            'id' => 5,
            'email' => 'crynobone@katsana.com',
            'name' => 'Mior Muhammad Zaki',
        ], $message);

        $this->assertTrue(isset($request['id']));
        $this->assertTrue(isset($request['email']));
        $this->assertTrue(isset($request['name']));

        $this->assertSame(5, $request['id']);
        $this->assertSame('crynobone@katsana.com', $request['email']);
        $this->assertSame('Mior Muhammad Zaki', $request['name']);
    }

    /** @test */
    public function it_has_access_to_object()
    {
        $message = new Message('platform', [], $this->mockServerRequestInterface());

        $request = new Request([
            'id' => 5,
            'email' => 'crynobone@katsana.com',
            'name' => 'Mior Muhammad Zaki',
        ], $message);

        $this->assertTrue(isset($request->id));
        $this->assertTrue(isset($request->email));
        $this->assertTrue(isset($request->name));

        $this->assertSame(5, $request->id);
        $this->assertSame('crynobone@katsana.com', $request->email);
        $this->assertSame('Mior Muhammad Zaki', $request->name);
    }

    protected function mockServerRequestInterface()
    {
        return \tap(m::mock(ServerRequestInterface::class), static function ($server) {
            $server->shouldReceive('getBody')->andReturn('');
        });
    }
}
