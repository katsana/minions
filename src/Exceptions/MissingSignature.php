<?php

namespace Minions\Exceptions;

use Datto\JsonRpc\Exceptions\Exception as JsonRpcException;

class MissingSignature extends JsonRpcException
{
    public function __construct()
    {
        parent::__construct('Missing Signature.', -32651);
    }
}
