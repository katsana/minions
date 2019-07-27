<?php

namespace Minions\Client;

use Clue\React\Buzz\Browser;
use Psr\Http\Message\ResponseInterface as ResponseContract;

class Project
{
    /**
     * Application ID for the client.
     *
     * @var string
     */
    protected $id;

    /**
     * Project configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The browser implementation.
     *
     * @var \Clue\React\Buzz\Browser
     */
    protected $browser;

    /**
     * Construct a new project.
     *
     * @param string                   $id
     * @param array                    $config
     * @param \Clue\React\Buzz\Browser $browser
     */
    public function __construct(string $id, array $config, Browser $browser)
    {
        $this->id = $id;
        $this->config = $config;
        $this->browser = $browser;
    }

    /**
     * Send message.
     *
     * @param \Minions\Client\MessageInterface $message
     *
     * @return \React\Promise\PromiseInterface
     */
    public function send(MessageInterface $message)
    {
        $headers = [
            'Content-Type' => 'application/json',
            'X-Request-ID' => $this->id,
            'Authorization' => "Token {$this->config['token']}",
            'X-Signature' => $message->signature($this->config['signature']),
        ];

        return $this->browser->post('/', $headers, $message->toJson())
                ->then(static function (ResponseContract $response) use ($message) {
                    return (new Response($response))->validate($message);
                });
    }

    /**
     * Broadcast message.
     *
     * @param \Minions\Client\MessageInterface $message
     *
     * @deprecated v0.5.0 To be removed on v1.0.0 release.
     *
     * @return \React\Promise\PromiseInterface
     *
     * @see static::send()
     */
    public function broadcast(MessageInterface $message)
    {
        return $this->send($message);
    }
}
