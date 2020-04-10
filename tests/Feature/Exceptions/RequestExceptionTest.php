<?php

namespace Minions\Tests\Feature\Exceptions;

use Illuminate\Support\Facades\Log;
use Minions\Client\Response;
use Minions\Exceptions\RequestException;
use Minions\Tests\TestCase;
use Mockery as m;

class RequestExceptionTest extends TestCase
{
    /** @test */
    public function it_can_report_exception_to_laravel()
    {
        $response = m::mock(Response::class);

        $response->shouldReceive('getRpcErrorData')->once()->andReturn([
            'username' => 'The username field is required.',
        ]);

        $exception = new RequestException('The given data was invalid.', -32602, $response, 'users.auth');

        Log::shouldReceive('error')->once()->andReturnUsing(function ($message, $context) {
            $this->assertSame('The given data was invalid.', $message);

            $this->assertSame('users.auth', $context['method']);
            $this->assertSame(['username' => 'The username field is required.'], $context['error']);
            $this->assertInstanceOf(RequestException::class, $context['exception']);
        });

        $exception->report();
    }
}
