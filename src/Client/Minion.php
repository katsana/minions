<?php

namespace Minions\Client;

use Graze\GuzzleHttp\JsonRpc\Client;
use InvalidArgumentException;
use Minions\Exceptions\ServerNotAvailable;
use React\Promise\Deferred;
use Throwable;

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
     * @return \React\Promise\Promise
     */
    public function broadcast(string $project, Notification $message)
    {
        $deferred = new Deferred();
        $config = $this->projectConfiguration($project);

        $options = [];

        if (\is_array($config['options'] ?? null) && ! empty($config['options'])) {
            $options = $config['options'];
        }

        $client = Client::factory($config['endpoint'], \array_merge($options, [
            'rpc_error' => true,
            'headers' => [
                'X-Request-ID' => $this->config['id'],
                'Authorization' => "Token {$config['token']}",
                'HTTP_X_SIGNATURE' => $message->signature($config['signature']),
            ],
        ]));

        try {
            $response = $client->send($message->asRequest($client));

            $deferred->resolve($response);
        } catch (InvalidArgumentException $e) {
            $deferred->reject(new ServerNotAvailable($config['endpoint']));
        } catch (Throwable $e) {
            $deferred->reject($e);
        }

        return $deferred->promise();
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
