<?php

namespace Minions\Tests\Stubs;

use Minions\Http\Request;
use Minions\Http\ValidatesRequests;

class User
{
    use ValidatesRequests;

    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'id' => ['required'],
            'email' => ['required', 'email'],
        ]);

        return $request->all();
    }
}
