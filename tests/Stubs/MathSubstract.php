<?php

namespace Minions\Tests\Stubs;

use Minions\Http\Message;

class MathSubstract
{
    public function __invoke($arguments)
    {
        return \array_sum($arguments);
    }

    public function authorize(Message $message): bool
    {
        return $message->id() === 'demo';
    }
}
