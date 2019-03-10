<?php

namespace Minions\Exceptions;

use Datto\JsonRpc\Exceptions\Exception as JsonRpcException;

class MisingSignature extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct('Missing Signature.', -32651);
    }
}
