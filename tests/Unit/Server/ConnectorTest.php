<?php

namespace Minions\Tests\Unit\Server;

use Illuminate\Contracts\Container\Container;
use Laravie\Stream\Logger;
use Minions\Server\Connector;
use Minions\Http\Router;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;

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

        $hostname = '0.0.0.0:8085';

        $logger->shouldReceive('info')->with("Server running at http://{$hostname}\n")->andReturnNull();

        $connector = new Connector($hostname, $eventLoop, $logger);

        $connector->handle(new Router($container, []), ['secure' => false]);

        $this->addToAssertionCount(1);

        $eventLoop->stop();
    }

    /** @test */
    public function it_can_create_secured_server()
    {
        $eventLoop = Factory::create();
        $logger = m::mock(Logger::class);
        $container = m::mock(Container::class);

        $hostname = '0.0.0.0:8086';

        $logger->shouldReceive('info')->with("Server running at https://{$hostname}\n")->andReturnNull();

        $connector = new Connector($hostname, $eventLoop, $logger);

        $connector->handle(new Router($container, []), ['secure' => true]);

        $this->addToAssertionCount(1);

        $eventLoop->stop();
    }
}
