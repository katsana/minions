<?php

namespace Minions\Exceptions;

class InvalidSignature extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid Signature.', -32652);
    }
}
