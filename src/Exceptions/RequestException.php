<?php

namespace Minions\Exceptions;

class RequestException extends \RuntimeException
{
    /**
     * The RPC Response implementation.
     *
     * @var \Minions\Client\ResponseInterface
     */
    protected $response;

    /**
     * Construct a request exception.
     *
     * @param string                            $message
     * @param int                               $code
     * @param \Minions\Client\ResponseInterface $response
     */
    public function __construct($message, $code, ResponseInterface $response)
    {
        parent::__construct($message, $code);

        $this->response = $response;
    }

    /**
     * Get the RPC Response.
     *
     * @return \Minions\Client\ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
