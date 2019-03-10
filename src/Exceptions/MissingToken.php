<?php

namespace Minions\Exceptions;

use Datto\JsonRpc\Exceptions\Exception as JsonRpcException;

class MisingToken extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct('Missing token.', -32651);
    }
}
