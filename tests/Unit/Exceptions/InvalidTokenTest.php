<?php

namespace Minions\Tests\Unit\Exceptions;

use Minions\Exceptions\InvalidToken;
use PHPUnit\Framework\TestCase;

class InvalidTokenTest extends TestCase
{
    /** @test */
    public function it_has_proper_signature()
    {
        $exception = new InvalidToken();

        $this->assertSame('Invalid Token.', $exception->getMessage());
        $this->assertSame(-32652, $exception->getCode());
    }
}
