<?php

namespace Minions\Server;

class Message
{
    public function status(): int
    {
        return 200;
    }

    public function headers(): array
    {
        return ['Content-Type' => 'text/plain'];
    }

    public function contents(): string
    {
        return 'Hello world';
    }
}
