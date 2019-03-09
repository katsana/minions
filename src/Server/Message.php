<?php

namespace Minions\Server;

use React\Http\Response;

class Message
{
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
        return ['Content-Type' => 'text/plain'];
    }

    /**
     * Message contents.
     *
     * @return string
     */
    public function body(): string
    {
        return 'Hello world';
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
