<?php

namespace Minions\Tests\Stubs;

class MathAdd
{
    public function __invoke($arguments)
    {
        return \array_sum($arguments);
    }
}
