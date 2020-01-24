<?php

namespace Minions\Client;

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
     * @param int|string|null $id
     */
    public function __construct(string $method, array $parameters, $id = null)
    {
        parent::__construct($method, $parameters);

        $this->id = $id ?? \time();
    }

    /**
     * Message ID.
     *
     * @return int|string|null
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Convert to JSON.
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
