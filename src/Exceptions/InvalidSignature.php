<?php

namespace Minions\Exceptions;

use Datto\JsonRpc\Exceptions\Exception as JsonRpcException;

class InvalidSignature extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct('Invalid Signature.', -32652);
    }
}
