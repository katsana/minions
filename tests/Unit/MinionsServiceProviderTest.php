<?php

namespace Minions\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Minions\MinionsServiceProvider;

class MinionsServiceProviderTest extends TestCase
{
    /** @test */
    public function it_declared_as_eagered_service_provider()
    {
        $provider = new MinionsServiceProvider(null);

        $this->assertFalse($provider->isDeferred());
    }
}
