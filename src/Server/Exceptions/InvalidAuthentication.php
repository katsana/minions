<?php

namespace App\JsonRpc\Exceptions;

use Exception;
use Datto\JsonRpc\Exception as JsonRpcException;

/**
 * Exception representing an invalid authentication/authorization attempt.
 * The error code corresponds to the JSON-RPC AuthX extension.
 *
 * @author Chad Kosie <ckosie@datto.com>
 */
class InvalidAuthentication extends Exception implements JsonRpcException
{
    public function __construct()
    {
        parent::__construct('Invalid auth.', -32652);
    }
}
