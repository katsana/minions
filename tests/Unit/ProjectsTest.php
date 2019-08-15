<?php

namespace Minions\Tests\Unit;

use Minions\Projects;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    /** @test */
    public function it_has_proper_signature()
    {
        $projects = new class() extends Projects {
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
        $projects = new class() extends Projects {
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
        $projects = new class() extends Projects {
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
}
