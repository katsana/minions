<?php

namespace Minions\Server;

use React\Http\Response;

class Reply
{
    /**
     * The body content.
     *
     * @var string
     */
    protected $body;

    /**
     * Construct a new reply.
     *
     * @param string $body [description]
     */
    public function __construct(string $body)
    {
    }

    /**
     * Message status.
     *
     * @return int
     */
    public function status(): int
    {
        return 200;
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
     * @return string
     */
    public function body(): string
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
