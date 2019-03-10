<?php

namespace Minions\Exceptions;

use Datto\JsonRpc\Exceptions\Exception as JsonRpcException;

class MissingToken extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct('Missing Token.', -32651);
    }
}
