<?php

namespace Minions\Tests\Unit\Exceptions;

use Minions\Exceptions\MissingToken;
use PHPUnit\Framework\TestCase;

class MissingTokenTest extends TestCase
{
    /** @test */
    public function it_has_proper_signature()
    {
        $exception = new MissingToken();

        $this->assertSame('Missing Token.', $exception->getMessage());
        $this->assertSame(-32651, $exception->getCode());
    }
}
