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
     */
    public function version(): string
    {
        return Client::VERSION;
    }

    /**
     * Method name.
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * Parameters.
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return \array_filter([
            'jsonrpc' => $this->version(),
            'method' => $this->method(),
            'params' => $this->parameters(),
        ]);
    }

    /**
     * Convert to JSON.
     */
    public function toJson(): string
    {
        return \json_encode($this->toArray());
    }

    /**
     * Message signature.
     */
    public function signature(string $secret): string
    {
        $signature = new Create($secret, 'sha256');

        return $signature($this->toJson(), Carbon::now()->timestamp);
    }
}
