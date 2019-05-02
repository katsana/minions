<?php

namespace Minions\Exceptions;

class InvalidSignature extends Exception
{
    /**
     * Construct invalid signature exception.
     */
    public function __construct()
    {
        parent::__construct('Invalid Signature.', -32652);
    }
}
