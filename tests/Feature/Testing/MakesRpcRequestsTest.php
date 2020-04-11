<?php

namespace Minions\Tests\Feature\Testing;

use Minions\Testing\MakesRpcRequests;
use Minions\Tests\HttpTestCase;

class MakesRpcRequestsTest extends HttpTestCase
{
    use MakesRpcRequests;

    /** @test */
    public function it_can_make_rpc_request()
    {
        $this->app['minions.router']->rpc('ping', 'Minions\Tests\Stubs\Ping');

        $response = $this->postRpc('ping', [1, 2, 3, 4, 5])->assertOk();

        $this->assertSame([1, 2, 3, 4, 5], $response->output());
    }
}
