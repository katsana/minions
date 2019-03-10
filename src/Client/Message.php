<?php

namespace Minions\Client;

use Graze\GuzzleHttp\JsonRpc\Client;

class Message extends Notification
{
    /**
     * Message ID.
     *
     * @var int|string
     */
    protected $id;

    /**
     * Construct a new Notification.
     *
     * @param string     $method
     * @param array      $parameters
     * @param int|string $id
     */
    public function __construct(string $method, array $parameters, $id)
    {
        parent::__construct($method, $parameters);

        $this->id = $id;
    }

    /**
     * Message ID.
     *
     * @return int|string
     */
    public function id()
    {
        return $this->id;
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
        return $client->request($this->id(), $this->method(), $this->parameters());
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
            'id' => $this->id(),
        ]));
    }
}
