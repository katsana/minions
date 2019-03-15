<?php

namespace Minions\Client;

interface MessageInterface
{
    /**
     * Json-RPC version.
     *
     * @return string
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
     *
     * @return string
     */
    public function method(): string;

    /**
     * Parameters.
     *
     * @return array
     */
    public function parameters(): array;

    /**
     * Convert to JSON.
     *
     * @return string
     */
    public function toJson(): string;

    /**
     * Message signature.
     *
     * @param string $secret
     *
     * @return string
     */
    public function signature(string $secret): string;
}
