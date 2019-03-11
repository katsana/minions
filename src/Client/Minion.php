<?php

namespace Minions\Client;

use Closure;
use Graze\GuzzleHttp\JsonRpc\Client;
use InvalidArgumentException;

class Minion
{
    /**
     * Configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Construct a new Minion.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Broadcast message.
     *
     * @param string                       $project
     * @param \Minions\Client\Notification $message
     *
     * @return void
     */
    public function broadcast(string $project, Notification $message, Closure $then): void
    {
        $config = $this->projectConfiguration($project);

        $options = [];

        if (\is_array($config['options']) && ! empty($config['options'])) {
            $options = $config['options'];
        }

        $client = Client::factory($config['endpoint'], \array_merge($options, [
            'headers' => [
                'X-Request-ID' => $this->config['id'],
                'Authorization' => "Token {$config['token']}",
                'HTTP_X_SIGNATURE' => $message->signature($config['signature']),
            ],
        ]));

        $response = $client->send($message->asRequest($client));

        $then($response);
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
