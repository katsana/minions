<?php

namespace Minions\Exceptions;

class MissingSignature extends Exception
{
    /**
     * Construct missing signature exception.
     */
    public function __construct()
    {
        parent::__construct('Missing Signature.', -32651);
    }
}
