<?php

namespace Minions\Tests\Unit\Exceptions;

use Minions\Client\ResponseInterface;
use Minions\Exceptions\RequestException;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class RequestExceptionTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_be_thrown()
    {
        $response = m::mock(ResponseInterface::class);

        $response->shouldReceive('getRpcErrorData')->andReturn(['project' => 'foo']);

        $exception = new RequestException(
            'Unable to find project', -32600, $response, 'math.add'
        );

        $this->assertSame($response, $exception->getResponse());
        $this->assertSame('Unable to find project', $exception->getMessage());
        $this->assertSame(-32600, $exception->getCode());
        $this->assertSame('math.add', $exception->getRequestMethod());

        $this->assertSame(['project' => 'foo'], $exception->getRpcErrorData());
    }
}
