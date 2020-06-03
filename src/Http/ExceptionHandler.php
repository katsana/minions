<?php

namespace Minions\Http;

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
     */
    protected function handleJsonRpcException(JsonRpcException $exception): Reply
    {
        $error = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ];

        if (! \is_null($data = $exception->getData())) {
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
     */
    protected function handleModelNotFoundException(ModelNotFoundException $exception): Reply
    {
        $error = [
            'code' => -32602,
            'message' => $exception->getMessage(),
            'exception' => \get_class($exception),
            'data' => [
                'model' => $exception->getModel(),
                'ids' => $exception->getIds(),
            ],
        ];

        return new Reply(\json_encode([
            'jsonrpc' => Server::VERSION,
            'id' => null,
            'error' => $error,
        ]));
    }

    /**
     * Handle Validation Exception.
     */
    protected function handleValidationException(ValidationException $exception): Reply
    {
        $error = [
            'code' => -32602,
            'message' => $exception->getMessage(),
            'exception' => \get_class($exception),
            'data' => $exception->errors() ?? null,
        ];

        return new Reply(\json_encode([
            'jsonrpc' => Server::VERSION,
            'id' => null,
            'error' => \array_filter($error),
        ]));
    }

    /**
     * Handle generic Exception.
     *
     * @param \Throwable|\Error $exception
     */
    protected function handleGenericException($exception): Reply
    {
        \report($exception);

        return new Reply(\json_encode([
            'jsonrpc' => Server::VERSION,
            'id' => null,
            'error' => [
                'code' => -32603,
                'message' => \get_class($exception).' - '.$exception->getMessage(),
                'exception' => \get_class($exception),
            ],
        ]));
    }
}
