<?php

namespace Minions\Tests\Stubs;

class Ping
{
    public function __invoke($arguments)
    {
        return \collect($arguments);
    }
}
