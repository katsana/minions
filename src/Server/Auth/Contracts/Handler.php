<?php

namespace Minions\Server\Auth\Contracts;

/**
 * This class is an authorization handler, it is used to authenticate requests made to the json-rpc server.
 *
 * @author Chad Kosie <ckosie@datto.com>
 */
interface Handler
{
    /**
     * Determines if this handler is capable of authorizing this request.
     *
     * @param string $method JSON-RPC method name
     * @param array $arguments JSON-RPC arguments array (positional or associative)
     *
     * @return bool
     */
    public function canHandle(string $method, array $arguments): bool;

    /**
     * Determines if this request is actually authenticated
     *
     * @param string $method JSON-RPC method name
     * @param array $arguments JSON-RPC arguments array (positional or associative)
     *
     * @return bool
     */
    public function authenticate(string $method, array $arguments): bool;
}
