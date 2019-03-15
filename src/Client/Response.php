<?php

namespace Minions\Client;

use Psr\Http\Message\ResponseInterface as ResponseContract;

class Response implements ResponseInterface
{
    /**
     * The PSR-7 Response implementation.
     *
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $original;

    /**
     * The response body.
     *
     * @var array|null
     */
    protected $content = [];

    /**
     * Construct response from PSR-7 Response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(ResponseContract $response)
    {
        $this->original = $response;

        if (\in_array($response->getStatusCode(), [200, 201, 204])) {
            $this->content = json_decode($response, true);
        }
    }

    /**
     * Validate response.
     *
     * @return $this
     */
    public function validate()
    {
        if (! \is_null($errorCode = $this->getRpcErrorCode())) {
            if (\in_array($errorCode, [-32600, -32601, -32602, -32700])) {
                new ClientHasError("[$errorCode] {$this->getRpcErrorMessage()}", $errorCode, $this);
            } else {
                new ServerHasError("[$errorCode] {$this->getRpcErrorMessage()}", $errorCode, $this);
            }
        }

        return $this;
    }

    /**
     * @return string|int|null
     */
    public function getRpcId()
    {
        return $this->content['id'] ?? null;
    }

    /**
     * @return mixed
     */
    public function getRpcResult()
    {
        return $this->content['result'] ?? null;
    }

    /**
     * @return string
     */
    public function getRpcVersion(): string
    {
        return $this->content['jsonrpc'];
    }

    /**
     * @return int|null
     */
    public function getRpcErrorCode(): ?int
    {
        return $this->content['error']['code'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getRpcErrorMessage(): ?string
    {
        return $this->content['error']['message'] ?? null;
    }

    /**
     * @return mixed
     */
    public function getRpcErrorData()
    {
        return $this->content['error']['data'] ?? null;
    }
}
