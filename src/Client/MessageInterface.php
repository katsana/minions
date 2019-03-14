<?php

namespace Minions\Client;

use Graze\GuzzleHttp\JsonRpc\ClientInterface;

interface MessageInterface
{
    /**
     * Convert to request.
     *
     * @param \Graze\GuzzleHttp\JsonRpc\ClientInterface $client
     *
     * @return object
     */
    public function asRequest(ClientInterface $client);

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
