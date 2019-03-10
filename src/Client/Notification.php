<?php

namespace Minions\Client;

use Carbon\Carbon;
use Graze\GuzzleHttp\JsonRpc\Client;

class Notification
{
    /**
     * Method name.
     *
     * @var string
     */
    protected $method;

    /**
     * Parameters.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Construct a new Notification.
     *
     * @param string $method
     * @param array  $parameters
     */
    public function __construct(string $method, array $parameters)
    {
        $this->method = $method;
        $this->parameters = $parameters;
    }

    /**
     * Json-RPC version.
     *
     * @return string
     */
    public function version(): string
    {
        return Client::SPEC;
    }

    /**
     * Method name.
     *
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * Parameters.
     *
     * @return array
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * Convert to request.
     *
     * @param \Graze\GuzzleHttp\JsonRpc\Client $client
     *
     * @return object
     */
    public function asRequest(Client $client)
    {
        return $client->notification($this->method(), $this->parameters());
    }

    /**
     * Convert to JSON.
     *
     * @return string
     */
    public function toJson(): string
    {
        return \json_encode(\array_filter([
            'jsonrpc' => $this->version(),
            'method' => $this->method(),
            'params' => $this->parameters(),
        ]));
    }

    /**
     * Message signature.
     *
     * @param string $secret
     *
     * @return string
     */
    public function signature(string $secret): string
    {
        $timestamp = (string) Carbon::now()->timestamp;
        $payload = "{$timestamp}.{$this->toJson()}";
        $signature = \hash_hmac('sha256', $payload, $secret);

        return "t={$timestamp},v1={$signature}";
    }
}
