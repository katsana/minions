<?php

namespace Minions\Client;

use Clue\React\Buzz\Browser;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as ResponseContract;
use React\EventLoop\Factory;

class Minion
{
    /**
     * Configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The event-loop implementation.
     *
     * @var \React\EventLoop\Factory
     */
    protected $eventLoop;

    /**
     * Construct a new Minion.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->eventLoop = Factory::create();
    }

    /**
     * Execute the loop.
     *
     * @return void
     */
    public function run(): void
    {
        $this->eventLoop->run();
    }

    /**
     * Broadcast message.
     *
     * @param string                           $project
     * @param \Minions\Client\MessageInterface $message
     *
     * @return \React\Promise\Promise
     */
    public function broadcast(string $project, MessageInterface $message)
    {
        $config = $this->projectConfiguration($project);

        $options = [];

        if (\is_array($config['options'] ?? null) && ! empty($config['options'])) {
            $options = $config['options'];
        }

        $endpoint = $config['endpoint'];
        $browser = $this->createBrowser($options);

        $headers = [
            'Content-Type' => 'application/json',
            'X-Request-ID' => $this->config['id'],
            'Authorization' => "Token {$config['token']}",
            'HTTP_X_SIGNATURE' => $message->signature($config['signature']),
        ];

        return $browser->post($endpoint, $headers, $message->toJson())
                ->then(function (ResponseContract $response) use ($message) {
                    return (new Response($response))->validate($message);
                });
    }

    /**
     * Create a new client using factory.
     *
     * @param array $options
     *
     * @return \Clue\React\Buzz\Browser
     */
    public function createBrowser(array $options): Browser
    {
        return (new Browser($this->eventLoop))
                    ->withOptions([
                        'timeout' => $options['timeout'] ?? null,
                        'followRedirects' => false,
                        'obeySuccessCode' => true,
                        'streaming' => false,
                    ]);
    }

    /**
     * Get configuration for a project.
     *
     * @param string|null $project
     *
     * @return array
     */
    protected function projectConfiguration(?string $project): array
    {
        if (\is_null($project) || ! \array_key_exists($project, $this->config['projects'])) {
            throw new InvalidArgumentException("Unable to find project [{$project}].");
        }

        return $this->config['projects'][$project];
    }
}
