<?php

namespace Minions\Tests\Unit;

use Minions\MinionsServiceProvider;
use PHPUnit\Framework\TestCase;

class MinionsServiceProviderTest extends TestCase
{
    /** @test */
    public function it_declared_as_eagered_service_provider()
    {
        $provider = new MinionsServiceProvider(null);

        $this->assertTrue($provider->isDeferred());
    }
}
