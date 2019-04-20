<?php

namespace Minions\Tests\Unit\Server;

use Mockery as m;
use Laravie\Stream\Logger;
use Minions\Server\Router;
use React\EventLoop\Factory;
use Minions\Server\Connector;
use PHPUnit\Framework\TestCase;
use React\EventLoop\LoopInterface;
use Illuminate\Contracts\Container\Container;

class ConnectorTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_create_unsecured_server()
    {
        $eventLoop = Factory::create();
        $logger = m::mock(Logger::class);
        $container = m::mock(Container::class);

        $hostname = "0.0.0.0:8085";

        $logger->shouldReceive('info')->with("Server running at http://{$hostname}\n")->andReturnNull();

        $connector = new Connector($hostname, $eventLoop, $logger);

        $connector->handle(new Router($container, []), ['secure' => false]);

        $this->addToAssertionCount(1);
    }
}
