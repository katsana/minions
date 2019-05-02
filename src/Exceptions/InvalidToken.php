<?php

namespace Minions\Exceptions;

class InvalidToken extends Exception
{
    /**
     * Construct invalid token exception.
     */
    public function __construct()
    {
        parent::__construct('Invalid Token.', -32652);
    }
}
