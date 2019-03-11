<?php

namespace Minions\Tests\Unit\Exceptions;

use Minions\Exceptions\ProjectNotFound;
use PHPUnit\Framework\TestCase;

class ProjectNotFoundTest extends TestCase
{
    /** @test */
    public function it_has_proper_signature()
    {
        $exception = new ProjectNotFound('foobar');

        $this->assertSame('Unable to find project: foobar', $exception->getMessage());
        $this->assertSame(-32600, $exception->getCode());
    }
}
