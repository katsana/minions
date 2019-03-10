<?php

namespace Minions\Exceptions;

use Datto\JsonRpc\Exceptions\Exception as JsonRpcException;

class InvalidToken extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct('Invalid Token.', -32652);
    }
}
