<?php

namespace Minions\Tests\Unit\Http;

use Illuminate\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler as IlluminateExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Minions\Exceptions\MissingSignature;
use Minions\Http\ExceptionHandler;
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
    public function it_can_handle_jsonrpc_exception()
    {
        $exception = new MissingSignature();

        $handler = new ExceptionHandler();

        $reply = $handler->handle($exception);

        $this->assertSame(
            '{"jsonrpc":"2.0","id":null,"error":{"code":-32651,"message":"Missing Signature."}}', $reply->body()
        );
    }

    /** @test */
    public function it_can_handle_model_not_found_exception()
    {
        $exception = new ModelNotFoundException();
        $exception->setModel('User', [2]);

        $handler = new ExceptionHandler();

        $reply = $handler->handle($exception);

        $this->assertSame(
            '{"jsonrpc":"2.0","id":null,"error":{"code":-32602,"message":"No query results for model [User] 2","exception":"Illuminate\\\Database\\\Eloquent\\\ModelNotFoundException","data":{"model":"User","ids":[2]}}}',
            $reply->body()
        );
    }

    /** @test */
    public function it_can_handle_validation_exception()
    {
        $validator = m::mock('Illuminate\Contracts\Validation\Validator');

        $validator->shouldReceive('errors->messages')->andReturn([
            'Password is required',
        ]);

        $handler = new ExceptionHandler();

        $reply = $handler->handle(new ValidationException($validator));

        $this->assertSame(
            '{"jsonrpc":"2.0","id":null,"error":{"code":-32602,"message":"The given data was invalid.","exception":"Illuminate\\\Validation\\\ValidationException","data":["Password is required"]}}',
            $reply->body()
        );
    }

    /** @test */
    public function it_can_handle_generic_exception()
    {
        Container::setInstance($app = new Container());

        $app->instance(IlluminateExceptionHandler::class, $reporter = m::mock(IlluminateExceptionHandler::class));

        $exception = new QueryException(
            'SELECT * FROM `users` WHERE email=?', ['crynobone@katsana.com'], m::mock('PDOException')
        );

        $reporter->shouldReceive('report')->once()->with($exception);

        $handler = new ExceptionHandler();

        $reply = $handler->handle($exception);

        $this->assertSame(
            '{"jsonrpc":"2.0","id":null,"error":{"code":-32603,"message":"Illuminate\\\Database\\\QueryException -  (SQL: SELECT * FROM `users` WHERE email=crynobone@katsana.com)","exception":"Illuminate\\\Database\\\QueryException"}}',
            $reply->body()
        );

        Container::setInstance(null);
    }
}
