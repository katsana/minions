<?php

namespace Minions\Tests\Feature\Concerns;

use Minions\Concerns\Configuration;
use Minions\Tests\TestCase;

class ConfigurationTest extends TestCase
{
    use Configuration;

    /** @test */
    public function it_can_retrieve_projects_from_array()
    {
        config([
            'minions' => ['id' => 'minions', 'projects' => ['platform' => ['endpoint' => 'http://rpc.localhost']]],
        ]);

        $config = $this->useConfigurationFrom($this->app);

        $this->assertSame('minions', $config['id']);
        $this->assertTrue(isset($config['projects']['platform']));
        $this->assertTrue(is_array($config['projects']));
        $this->assertTrue(is_array($config['projects']['platform']));
        $this->assertSame('http://rpc.localhost', $config['projects']['platform']['endpoint']);
    }

    /** @test */
    public function it_can_retrieve_projects_from_class()
    {
        config([
            'minions' => ['id' => 'minions', 'projects' => 'Minions\Tests\Feature\Concerns\StubFinder'],
        ]);

        $config = $this->useConfigurationFrom($this->app);

        $this->assertSame('minions', $config['id']);
        $this->assertTrue($config['projects'] instanceof \ArrayAccess);
        $this->assertTrue(isset($config['projects']['platform']));
        $this->assertTrue(is_array($config['projects']['platform']));
        $this->assertSame('http://rpc.localhost', $config['projects']['platform']['endpoint']);
    }
}

class StubFinder extends \Minions\Finder
{
    protected $projects = [
        'platform' => ['endpoint' => 'http://rpc.localhost'],
    ];
}
