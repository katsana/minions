<?php

namespace Minions\Tests\Stubs;

use Minions\Http\Request;

class MathAdd
{
    public function __invoke(Request $request)
    {
        return \array_sum($request->all());
    }

    public function authorize(Request $request): bool
    {
        return $request->id() === 'demo';
    }
}
