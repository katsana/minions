<?php

namespace Minions\Exceptions;

use Minions\Client\ResponseInterface;
use RuntimeException;

class RequestException extends RuntimeException
{
    /**
     * The RPC Response implementation.
     *
     * @var \Minions\Client\ResponseInterface
     */
    protected $response;

    /**
     * The requested method.
     *
     * @var string
     */
    protected $requestMethod;

    /**
     * Construct a request exception.
     *
     * @param string $message
     * @param int    $code
     */
    public function __construct($message, $code, ResponseInterface $response, string $requestMethod)
    {
        parent::__construct($message, $code);

        $this->response = $response;
        $this->requestMethod = $requestMethod;
    }

    /**
     * Get the RPC Response.
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Get requested method.
     */
    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }

    /**
     * Get RPC error data.
     *
     * @return mixed
     */
    public function getRpcErrorData()
    {
        return $this->response->getRpcErrorData();
    }
}
