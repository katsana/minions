<?php

namespace Minions\Client;

use Graze\GuzzleHttp\JsonRpc\Client;
use Minions\Exceptions\ClientHasError;
use Minions\Exceptions\ServerHasError;
use Minions\Exceptions\ServerNotAvailable;
use React\Promise\Deferred;

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
     * @param string                           $project
     * @param \Minions\Client\MessageInterface $message
     *
     * @return \React\Promise\Promise
     */
    public function broadcast(string $project, MessageInterface $message)
    {
        $deferred = new Deferred();
        $config = $this->projectConfiguration($project);

        $options = [];

        if (\is_array($config['options'] ?? null) && ! empty($config['options'])) {
            $options = $config['options'];
        }

        $client = $this->createClient($config['endpoint'], \array_merge($options, [
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
        } catch (\InvalidArgumentException $e) {
            $deferred->reject(new ServerNotAvailable($config['endpoint']));
        } catch (\Graze\GuzzleHttp\JsonRpc\Exception\ClientException $e) {
            $deferred->reject(new ClientHasError(
                $e->getMessage(), $e->getRequest(), $e->getResponse(), $e->getPrevious()
            ));
        } catch (\Graze\GuzzleHttp\JsonRpc\Exception\ServerException $e) {
            $deferred->reject(new ServerHasError(
                $e->getMessage(), $e->getRequest(), $e->getResponse(), $e->getPrevious()
            ));
        } catch (\Throwable $e) {
            $deferred->reject($e);
        }

        return $deferred->promise();
    }

    /**
     * Create a new client using factory.
     *
     * @param string $endpoint
     * @param array  $options
     *
     * @return \Graze\GuzzleHttp\JsonRpc\Client
     */
    public function createClient(string $endpoint, array $options): Client
    {
        return Client::factory($endpoint, $options);
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
