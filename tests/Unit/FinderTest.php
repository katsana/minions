<?php

namespace Minions\Tests\Unit;

use Minions\Finder;
use PHPUnit\Framework\TestCase;

class FinderTest extends TestCase
{
    /** @test */
    public function it_has_proper_signature()
    {
        $projects = new class() extends Finder {
            protected $projects = [
                'platform' => [
                    'endpoint' => 'https://rpc.localhost',
                ],
            ];
        };

        $this->assertTrue(isset($projects['platform']));
        $this->assertSame(['endpoint' => 'https://rpc.localhost'], $projects['platform']);
        $this->assertSame('https://rpc.localhost', $projects['platform']['endpoint']);
    }

    /** @test */
    public function it_can_register_new_project_as_server()
    {
        $projects = new class() extends Finder {
            //
        };

        $projects->register('platform', 'foobar', 'hello', 'http://rpc.localhost');

        $this->assertTrue(isset($projects['platform']));
        $this->assertSame([
            'endpoint' => 'http://rpc.localhost',
            'token' => 'foobar',
            'signature' => 'hello',
            'options' => [],
        ], $projects['platform']);
    }

    /** @test */
    public function it_can_register_new_project_as_client()
    {
        $projects = new class() extends Finder {
            //
        };

        $projects->register('platform', 'foobar', 'hello');

        $this->assertTrue(isset($projects['platform']));
        $this->assertSame([
            'endpoint' => null,
            'token' => 'foobar',
            'signature' => 'hello',
            'options' => [],
        ], $projects['platform']);
    }

    /** @test */
    public function it_use_manually_set_a_project()
    {
        $projects = new class() extends Finder {
            //
        };

        $this->assertFalse(isset($projects['platform']));

        $projects['platform'] = [
            'endpoint' => 'https://rpc.localhost',
            'token' => 'foobar',
            'signature' => 'hello',
        ];

        $this->assertSame([
            'endpoint' => 'https://rpc.localhost',
            'options' => [],
            'token' => 'foobar',
            'signature' => 'hello'
        ], $projects['platform']);
        $this->assertTrue(isset($projects['platform']));
    }

    /** @test */
    public function it_use_manually_unset_a_project()
    {
        $projects = new class() extends Finder {
            //
        };

        $projects->register('platform', 'foobar', 'hello', 'http://rpc.localhost');

        $this->assertTrue(isset($projects['platform']));

        unset($projects['platform']);

        $this->assertFalse(isset($projects['platform']));
    }
}
