<?php

namespace Minions\Exceptions;

class InvalidToken extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid Token.', -32652);
    }
}
