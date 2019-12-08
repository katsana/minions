<?php

namespace Minions\Client;

interface MessageInterface
{
    /**
     * Json-RPC version.
     */
    public function version(): string;

    /**
     * Message ID.
     *
     * @return int|string|null
     */
    public function id();

    /**
     * Method name.
     */
    public function method(): string;

    /**
     * Parameters.
     */
    public function parameters(): array;

    /**
     * Convert to JSON.
     */
    public function toJson(): string;

    /**
     * Message signature.
     */
    public function signature(string $secret): string;
}
