<?php

namespace Minions\Client;

use Carbon\Carbon;
use Datto\JsonRpc\Client;
use Laravie\Codex\Security\TimeLimitSignature\Create;

class Notification implements MessageInterface
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
     * Message ID.
     *
     * @return null
     */
    public function id()
    {
        return null;
    }

    /**
     * Json-RPC version.
     *
     * @return string
     */
    public function version(): string
    {
        return Client::VERSION;
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
        $signature = new Create($secret, 'sha256');

        return $signature($this->toJson(), Carbon::now()->timestamp);
    }
}
