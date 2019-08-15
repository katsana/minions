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
}
