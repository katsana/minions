<?php

namespace Minions\Tests\Unit\Exceptions;

use Minions\Exceptions\InvalidSignature;
use PHPUnit\Framework\TestCase;

class InvalidSignatureTest extends TestCase
{
    /** @test */
    public function it_has_proper_signature()
    {
        $exception = new InvalidSignature();

        $this->assertSame('Invalid Signature.', $exception->getMessage());
        $this->assertSame(-32652, $exception->getCode());
    }
}
