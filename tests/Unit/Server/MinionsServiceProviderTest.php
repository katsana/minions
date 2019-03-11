<?php

namespace Minions\Tests\Unit\Server;

use Minions\Server\MinionsServiceProvider;
use PHPUnit\Framework\TestCase;

class MinionsServiceProviderTest extends TestCase
{
    /** @test */
    public function it_declared_as_deferred_service_provider()
    {
        $provider = new MinionsServiceProvider(null);

        $this->assertTrue($provider->isDeferred());
    }

    /** @test */
    public function it_only_loaded_when_minions_service_provider_has_been_loaded()
    {
        $provider = new MinionsServiceProvider(null);

        $this->assertSame(['Minions\MinionsServiceProvider'], $provider->when());
    }
}
