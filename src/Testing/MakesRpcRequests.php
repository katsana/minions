<?php

namespace Minions\Testing;

use Minions\Minion;
use Psr\Http\Message\ServerRequestInterface;

trait MakesRpcRequests
{
    /**
     * Get minion configuration.
     */
    protected function getMinionConfiguration(string $clientId, string $serverId): array
    {
        return [
            'minions' => [
                'id' => $serverId,
                'projects' => [
                    "{$clientId}" => [
                        'endpoint' => 'http://localhost/_minions-rpc',
                        'token' => 'secret-token',
                        'signature' => 'secret-signature',
                    ],
                    "{$serverId}" => [
                        'endpoint' => 'http://localhost/_minions-rpc',
                        'token' => 'secret-token',
                        'signature' => 'secret-signature',
                    ],
                ],
            ],
        ];
    }

    /**
     * Send using RPC.
     *
     * @return \Illuminate\Foundation\Testing\TestResponse|\Illuminate\Testing\TestResponse
     */
    protected function sendRpc(
        string $method,
        array $parameters = [],
        string $clientId = 'client-project-id',
        string $serverId = 'server-project-id'
    ) {
        \tap($this->app->make('minions.config'), function ($config) use ($clientId, $serverId) {
            $config->set($this->getMinionConfiguration($clientId, $serverId)['minions']);
        });

        $this->app->make('router')->post('_minions-rpc', function (ServerRequestInterface $request) {
            $reply = $this->app->make('minions.router')->handle($request);

            return \response(
                $reply->body(), $reply->status(), $reply->headers()
            );
        });

        $message = Minion::message($method, $parameters);

        return TestResponse::fromBaseResponse($this->postJson('_minions-rpc', $message->toArray(), [
            'X-Request-ID' => $clientId,
            'Authorization' => 'Token secret-token',
            'X-Signature' => $message->signature('secret-signature'),
            'Content-Type' => 'application/json',
        ]));
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
        return $this->sendRpc($method, $parameters, $clientId, $serverId);
    }
}
