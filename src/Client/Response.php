<?php

namespace Minions\Client;

use Minions\Exceptions\ClientHasError;
use Minions\Exceptions\ServerHasError;
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
    protected $content = [
        'jsonrpc' => '2.0',
    ];

    /**
     * Construct response from PSR-7 Response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(ResponseContract $response)
    {
        $this->original = $response;

        if (\in_array($response->getStatusCode(), [200, 201])) {
            $this->content = \json_decode((string) $response->getBody(), true);
        }
    }

    /**
     * Validate response.
     *
     * @return $this
     */
    public function validate(MessageInterface $message)
    {
        if (! \is_null($errorCode = $this->getRpcErrorCode())) {
            if (\in_array($errorCode, [-32600, -32601, -32602, -32700])) {
                throw new ClientHasError($this->getRpcErrorMessage(), $errorCode, $this, $message->method());
            } else {
                throw new ServerHasError($this->getRpcErrorMessage(), $errorCode, $this, $message->method());
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
