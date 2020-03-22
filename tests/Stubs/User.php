<?php

namespace Minions\Tests\Stubs;

use Minions\Http\Message;
use Minions\Http\ValidatesRequests;

class User
{
    use ValidatesRequests;

    public function __invoke($arguments)
    {
        $this->validate($arguments, [
            'id' => ['required'],
            'email' => ['required', 'email'],
        ]);

        return $arguments;
    }
}
