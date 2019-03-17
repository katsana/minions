<?php

namespace Minions\Tests\Unit\Server;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Minions\Server\ExceptionHandler;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ExceptionHandlerTest extends TestCase
{
    /**
     * Teardown the test environment.
     */
    protected function tearDown(): void
    {
        m::close();
    }

    /** @test */
    public function it_can_handle_model_not_found_exception()
    {
        $exception = new ModelNotFoundException();
        $exception->setModel('User', [2]);

        $handler = new ExceptionHandler();

        $reply = $handler->handle($exception);

        $this->assertSame('{"jsonrpc":"2.0","id":null,"error":{"code":-32602,"message":"No query results for model [User] 2","data":[2]}}', $reply->body());
    }

    /** @test */
    public function it_can_handle_validation_exception_exception()
    {
        $validator = m::mock('Illuminate\Contracts\Validation\Validator');

        $validator->shouldReceive('errors->messages')->andReturn([
            'Password is required',
        ]);

        $handler = new ExceptionHandler();

        $reply = $handler->handle(new ValidationException($validator));

        $this->assertSame(
            '{"jsonrpc":"2.0","id":null,"error":{"code":-32602,"message":"The given data was invalid.","data":["Password is required"]}}', $reply->body()
        );
    }
}
