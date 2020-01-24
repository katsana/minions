<?php

namespace Minions\Tests\Unit\Http;

use Minions\Http\MinionsServiceProvider;
use PHPUnit\Framework\TestCase;

class MinionsServiceProviderTest extends TestCase
{
    /** @test */
    public function it_declared_as_deferred_service_provider()
    {
        $provider = new MinionsServiceProvider(null);

        $this->assertTrue($provider->isDeferred());
        $this->assertSame(['minions.controller', 'minions.router'], $provider->provides());
    }
}
