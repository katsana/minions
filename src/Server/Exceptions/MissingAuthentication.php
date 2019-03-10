<?php

namespace Katsana\Minions\Exceptions;

use Datto\JsonRpc\Exception as JsonRpcException;
use Exception;

/**
 * Exception representing missing authentication credentials.
 * The error code corresponds to the JSON-RPC AuthX extension.
 *
 * @author Chad Kosie <ckosie@datto.com>
 */
class MissingAuthentication extends Exception implements JsonRpcException
{
    public function __construct()
    {
        parent::__construct('Missing auth.', -32651);
    }
}
