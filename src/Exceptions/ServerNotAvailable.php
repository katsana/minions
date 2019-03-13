<?php

namespace Minions\Exceptions;

use RuntimeException;

class ServerNotAvailable extends RuntimeException
{
    public function __construct(string $endpoint)
    {
        parent::__construct("RPC Server [{$endpoint}] not available.");
    }
}
