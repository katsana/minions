<?php

namespace Minions\Tests\Feature\Http\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Canvas\Core\Testing\TestCase;

class MakeRpcRequestTest extends TestCase
{
    protected $files = [
        'app/JsonRpc/Ping.php',
    ];

    /** @test */
    public function it_can_generate_rpc_request_file()
    {
        $this->artisan('minions:make', ['name' => 'Ping'])
            ->assertExitCode(0);

        $this->assertFileContains([
            'namespace App\JsonRpc;',
            'use Minions\Http\Message;',
            'class Ping',
            'public function __invoke(array $arguments, Message $message)',
        ], 'app/JsonRpc/Ping.php');
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['Minions\Http\MinionsServiceProvider'];
    }
}
