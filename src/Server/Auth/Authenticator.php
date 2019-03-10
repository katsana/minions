<?php

namespace Minions\Server\Auth;

use Minions\Server\Exceptions\InvalidAuthentication;
use Minions\Server\Exceptions\MissingAuthentication;

/**
 * This class authorizes requests by iterating over the provided authentication handlers,
 * attempting to authorize the request with each handler.
 *
 * @author Chad Kosie <ckosie@datto.com>, Philipp Heckel <ph@datto.com>
 */
class Authenticator
{
    /**
     * @var Handler[]
     */
    private $handlers;

    /**
     * @param Handler[] $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     * Add an authentication handler to be iterated over when attempting to authorize a request.
     *
     * @param \Minions\Server\Auth\Contracts\Handler $handler
     */
    public function addHandler(Contracts\Handler $handler): void
    {
        $this->handlers[] = $handler;
    }

    /**
     * Returns all authentication handlers currently attached.
     *
     * @return Handler[]
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * Attempt to authorize a request. This will iterate over all authentication handlers that can handle this type of
     * request. It will stop after it has found one that can authenticate the request.
     *
     * @param string $method    JSON-RPC method name
     * @param array  $arguments JSON-RPC arguments array (positional or associative)
     *
     * @throws MissingAuth If the no credentials are given
     * @throws InvalidAuth If the given credentials are invalid
     *
     * @return void
     */
    public function authenticate(string $method, array $arguments): void
    {
        $handlers = $this->filterHandlers($method, $arguments);

        if (count($handlers) > 0) {
            foreach ($handlers as $handler) {
                $isAuthenticated = $handler->authenticate($method, $arguments);

                if ($isAuthenticated) {
                    return;
                }
            }

            throw new InvalidAuthentication();
        } else {
            throw new MissingAuthentication();
        }
    }

    /**
     * Filters the handlers array down to only the handlers that can handle
     * the given request.
     *
     * @param string $method    JSON-RPC method name
     * @param array  $arguments JSON-RPC arguments array (positional or associative)
     *
     * @return \Minions\Server\Auth\Contracts\Handler[] Filtered list of handlers
     */
    private function filterHandlers(string $method, array $arguments): array
    {
        $handlers = [];

        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($method, $arguments)) {
                $handlers[] = $handler;
            }
        }

        return $handlers;
    }
}
