<?php

namespace Minions\Tests\Unit\Exceptions;

use Minions\Exceptions\MissingSignature;
use PHPUnit\Framework\TestCase;

class MissingSignatureTest extends TestCase
{
    /** @test */
    public function it_has_proper_signature()
    {
        $exception = new MissingSignature();

        $this->assertSame('Missing Signature.', $exception->getMessage());
        $this->assertSame(-32651, $exception->getCode());
    }
}
