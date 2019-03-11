<?php

namespace Minions\Tests\Stubs;

class AddMath
{
    public function __invoke($arguments)
    {
        return \array_sum($arguments);
    }
}
