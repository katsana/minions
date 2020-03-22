<?php

namespace Minions\Exceptions;

use Datto\JsonRpc\Exceptions\Exception as JsonRpcException;

class Exception extends JsonRpcException
{
    /**
     * Trigger "Internal error" exception.
     *
     * @param mixed $data
     *
     * @return static
     */
    public static function internalError(string $message = 'Internal error', $data = null)
    {
        return new static($message, -32603, $data);
    }

    /**
     * Trigger "Parse error" exception.
     *
     * @param mixed $data
     *
     * @return static
     */
    public static function parseError(string $message = 'Parse error', $data = null)
    {
        return new static($message, -32700, $data);
    }

    /**
     * Trigger "Invalid request" exception.
     *
     * @param mixed $data
     *
     * @return static
     */
    public static function invalidRequest(string $message = 'Invalid request', $data = null)
    {
        return new static($message, -32600, $data);
    }

    /**
     * Trigger "Invalid parameters" exception.
     *
     * @param mixed $data
     *
     * @return static
     */
    public static function invalidParameters(string $message = 'Invalid parameters', $data = null)
    {
        return new static($message, -32602, $data);
    }

    /**
     * Trigger "Method not found" exception.
     *
     * @param mixed $data
     *
     * @return static
     */
    public static function methodNotFound(string $message = 'Method not found', $data = null)
    {
        return new static($message, -32601, $data);
    }
}
