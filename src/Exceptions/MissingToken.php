<?php

namespace Minions\Exceptions;

class MissingToken extends Exception
{
    public function __construct()
    {
        parent::__construct('Missing Token.', -32651);
    }
}
