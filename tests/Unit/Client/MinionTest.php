<?php

namespace Minions\Test\Unit\Client;

use Minions\Client\Minion;
use Minions\Client\Project;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use React\EventLoop\LoopInterface;

class MinionTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_configure_existing_project()
    {
        $minion = new Minion(m::mock(LoopInterface::class), [
            'id' => 'foobar',
            'projects' => [
                'platform' => [
                    'endpoint' => 'https://127.0.0.1:6005',
                    'token' => 'secret',
                    'signature' => 'secret',
                ],
            ],
        ]);

        $project = $minion->project('platform');

        $this->assertInstanceOf(Project::class, $project);
    }

    /** @test */
    public function it_cant_configure_none_existing_project()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Unable to find project [katsana].');

        $minion = new Minion(m::mock(LoopInterface::class), [
            'id' => 'foobar',
            'projects' => [
                'platform' => [
                    'endpoint' => 'https://127.0.0.1:6005',
                    'token' => 'secret',
                    'signature' => 'secret',
                ],
            ],
        ]);

        $project = $minion->project('katsana');
    }

    /** @test */
    public function it_can_replace_event_loop()
    {
        $eventLoop1 = m::mock(LoopInterface::class);
        $eventLoop2 = m::mock(LoopInterface::class);

        $minion = new Minion($eventLoop1, [
            'id' => 'foobar',
            'projects' => [
                'platform' => [
                    'endpoint' => 'https://127.0.0.1:6005',
                    'token' => 'secret',
                    'signature' => 'secret',
                ],
            ],
        ]);

        $this->assertSame($eventLoop1, $minion->getEventLoop());

        $minion->setEventLoop($eventLoop2);

        $this->assertSame($eventLoop2, $minion->getEventLoop());
    }

    /** @test */
    public function it_can_be_run()
    {
        $eventLoop = m::mock(LoopInterface::class);

        $eventLoop->shouldReceive('run')->andReturnUsing(function () {
            $this->addToAssertionCount(1);
        });

        $minion = new Minion($eventLoop, [
            'id' => 'foobar',
            'enabled' => true,
            'projects' => [
                'platform' => [
                    'endpoint' => 'https://127.0.0.1:6005',
                    'token' => 'secret',
                    'signature' => 'secret',
                ],
            ],
        ]);

        $minion->run();
    }
}
