<?php

namespace Minions\Exceptions;

class MissingSignature extends Exception
{
    public function __construct()
    {
        parent::__construct('Missing Signature.', -32651);
    }
}
