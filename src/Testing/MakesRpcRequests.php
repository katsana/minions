<?php

namespace Minions\Testing;

use Minions\Minion;

trait MakesRpcRequests
{
    /**
     * Get minion configuration.
     */
    protected function getMinionConfiguration(string $clientId, string $serverId): array
    {
        return [
            'minions.id' => $serverId,
            'minions.projects' => [
                "{$clientId}" => [
                    'token' => 'secret-token',
                    'signature' => 'secret-signature',
                ],
                "{$serverId}" => [
                    'endpoint' => 'http://localhost/rpc',
                    'token' => 'secret-token',
                    'signature' => 'secret-signature',
                ],
            ],
        ];
    }

    /**
     * Post using RPC.
     *
     * @return \Illuminate\Foundation\Testing\TestResponse|\Illuminate\Testing\TestResponse
     */
    protected function postRpc(
        string $method,
        array $parameters = [],
        string $clientId = 'client-project-id',
        string $serverId = 'server-project-id'
    ) {
        $config = \tap($this->app->make('config'), function ($config) use ($clientId, $serverId) {
            $config->set($this->getMinionConfiguration($clientId, $serverId));
        });

        $message = Minion::message($method, $parameters);

        return $this->postJson('rpc', $message->toArray(), [
            'X-Request-ID' => $clientId,
            'Authorization' => 'Token secret-token',
            'X-Signature' => $message->signature('secret-signature'),
            'Content-Type' => 'application/json',
        ]);
    }
}
