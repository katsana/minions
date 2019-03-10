<?php

namespace Minions;

use Closure;
use InvalidArgumentException;
use Minions\Client\Notification;

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

        $client = Client::factory($config['endpoint'], [
            'headers' => [
                'X-Request-ID' => $this->config['id'],
                'Authorization' => "Token {$config['token']}",
            ],
        ]);

        $promise = $client->sendAsync($message->asRequest($client));
        $promise->then($then);
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
        if (is_null($project) || ! array_key_exists($project, $this->config['projects'])) {
            return new InvalidArgumentException("Unable to find project [{$project}].");
        }

        return $this->config['projects'][$project];
    }
}
