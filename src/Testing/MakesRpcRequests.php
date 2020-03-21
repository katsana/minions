<?php

namespace Minions\Testing;

use Illuminate\Contracts\Config\Repository;

trait MakesRpcRequests
{
    /**
     * Setup Minions configuration.
     */
    protected function setUpMinionConfiguration(Repository $config): void
    {
        $config->set([
            'minions.id' => 'server-project-id',
            'minions.projects' => [
                'client-project-id' => [
                    'token' => 'secret-token',
                    'signature' => 'secret-signature',
                ],
                'server-project-id' => [
                    'endpoint' => 'http://localhost/rpc',
                    'token' => 'secret-token',
                    'signature' => 'secret-signature',
                ],
            ],
        ]);
    }

    /**
     * Post using RPC.
     *
     * @return \Illuminate\Foundation\Testing\TestResponse|\Illuminate\Testing\TestResponse
     */
    protected function postRpc(string $method, array $parameters = [])
    {
        $this->setUpMinionConfiguration($this->app->make('config'));

        $message = Minion::message($method, $parameters);

        return $this->postJson('rpc', $message->toArray(), [
            'X-Request-ID' => 'client-project-id',
            'Authorization' => 'Token secret-token',
            'X-Signature' => $message->signature('secret-signature'),
            'Content-Type' => 'application/json',
        ]);
    }
}
