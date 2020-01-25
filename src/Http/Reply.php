<?php

namespace Minions\Http;

class Reply
{
    /**
     * The body content.
     *
     * @var string|null
     */
    protected $body;

    /**
     * Construct a new reply.
     */
    public function __construct(?string $body)
    {
        $this->body = $body;
    }

    /**
     * Message status.
     */
    public function status(): int
    {
        return \is_null($this->body) ? 204 : 200;
    }

    /**
     * Message headers.
     */
    public function headers(): array
    {
        return ['Content-Type' => 'application/json'];
    }

    /**
     * Message contents.
     */
    public function body(): ?string
    {
        return $this->body;
    }
}
