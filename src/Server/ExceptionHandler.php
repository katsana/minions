<?php

namespace Minions\Server;

use Datto\JsonRpc\Exceptions\Exception as JsonRpcException;
use Datto\JsonRpc\Server;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class ExceptionHandler
{
    /**
     * Handle exception occured during request and check if we can transform it
     * to JSON-RPC response.
     *
     * @param \Throwable $exception
     *
     * @throws \Throwable
     *
     * @return \Minions\Server\Reply
     */
    public function handle($exception): Reply
    {
        if ($exception instanceof ModelNotFoundException) {
            return $this->handleModelNotFoundException($exception);
        } elseif ($exception instanceof ValidationException) {
            return $this->handleValidationException($exception);
        } elseif ($exception instanceof JsonRpcException) {
            return $this->handleJsonRpcException($exception);
        }

        return $this->handleGenericException($exception);
    }

    /**
     * Handle Json-RPC Exception.
     *
     * @param \Datto\JsonRpc\Exceptions\Exception $exception
     *
     * @return \Minions\Server\Reply
     */
    protected function handleJsonRpcException(JsonRpcException $exception): Reply
    {
        $error = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ];

        $data = $exception->getData();

        if ($data !== null) {
            $error['data'] = $data;
        }

        return new Reply(\json_encode([
            'jsonrpc' => Server::VERSION,
            'id' => null,
            'error' => $error,
        ]));
    }

    /**
     * Handle Model not found Exception.
     *
     * @param \Illuminate\Database\Eloquent\ModelNotFoundException $exception
     *
     * @return \Minions\Server\Reply
     */
    protected function handleModelNotFoundException(ModelNotFoundException $exception): Reply
    {
        $error = [
            'code' => -32602,
            'message' => $exception->getMessage(),
        ];

        $data = $exception->getIds();

        if ($data !== null) {
            $error['data'] = $data;
        }

        return new Reply(\json_encode([
            'jsonrpc' => Server::VERSION,
            'id' => null,
            'error' => $error,
        ]));
    }

    /**
     * Handle Validation Exception.
     *
     * @param \Illuminate\Validation\ValidationException $exception
     *
     * @return \Minions\Server\Reply
     */
    protected function handleValidationException(ValidationException $exception): Reply
    {
        $error = [
            'code' => -32602,
            'message' => $exception->getMessage(),
        ];

        $data = $exception->errors();

        if ($data !== null) {
            $error['data'] = $data;
        }

        return new Reply(\json_encode([
            'jsonrpc' => Server::VERSION,
            'id' => null,
            'error' => $error,
        ]));
    }

    /**
     * Handle generic Exception.
     *
     * @param \Throwable|\Error $exception
     *
     * @return \Minions\Server\Reply
     */
    protected function handleGenericException($exception): Reply
    {
        return new Reply(\json_encode([
            'jsonrpc' => Server::VERSION,
            'id' => null,
            'error' => [
                'code' => -32603,
                'message' => get_class($exception)." - ".$exception->getMessage(),
            ],
        ]));
    }
}
