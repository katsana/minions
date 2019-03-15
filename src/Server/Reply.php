<?php

namespace Minions\Server;

use React\Http\Response;

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
     *
     * @param string|null $body
     */
    public function __construct(?string $body)
    {
        $this->body = $body;
    }

    /**
     * Message status.
     *
     * @return int
     */
    public function status(): int
    {
        return \is_null($this->body) ? 204 : 200;
    }

    /**
     * Message headers.
     *
     * @return array
     */
    public function headers(): array
    {
        return ['Content-Type' => 'application/json'];
    }

    /**
     * Message contents.
     *
     * @return string|null
     */
    public function body(): ?string
    {
        return $this->body;
    }

    /**
     * Convert message to response.
     *
     * @return \React\Http|Response
     */
    public function asResponse(): Response
    {
        return new Response(
            $this->status(), $this->headers(), $this->body()
        );
    }
}
