<?php

namespace Minions\Tests\Stubs;

use Minions\Http\Request;

class Ping
{
    public function __invoke(Request $request)
    {
        return \collect($request);
    }
}
