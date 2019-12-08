<?php

namespace Minions\Tests\Unit\Exceptions;

use Minions\Exceptions\Exception;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    /** @test */
    public function it_can_trigger_internal_error_exception()
    {
        $exception = Exception::internalError('Internal Error');

        $this->assertSame('Internal Error', $exception->getMessage());
        $this->assertSame(-32603, $exception->getCode());
    }

    /** @test */
    public function it_can_trigger_parse_error_exception()
    {
        $exception = Exception::parseError('Parse Error');

        $this->assertSame('Parse Error', $exception->getMessage());
        $this->assertSame(-32700, $exception->getCode());
    }

    /** @test */
    public function it_can_trigger_invalid_request_exception()
    {
        $exception = Exception::invalidRequest('Invalid Request');

        $this->assertSame('Invalid Request', $exception->getMessage());
        $this->assertSame(-32600, $exception->getCode());
    }

    /** @test */
    public function it_can_trigger_invalid_parameters_exception()
    {
        $exception = Exception::invalidParameters('Invalid Parameters');

        $this->assertSame('Invalid Parameters', $exception->getMessage());
        $this->assertSame(-32602, $exception->getCode());
    }

    /** @test */
    public function it_can_trigger_method_not_found_exception()
    {
        $exception = Exception::methodNotFound('Method Not Found');

        $this->assertSame('Method Not Found', $exception->getMessage());
        $this->assertSame(-32601, $exception->getCode());
    }
}
