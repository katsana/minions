<?php

namespace Minions\Exceptions;

class MissingToken extends Exception
{
    /**
     * Construct missing token exception.
     */
    public function __construct()
    {
        parent::__construct('Missing Token.', -32651);
    }
}
