<?php

namespace Minions\Client;

interface ResponseInterface
{
    /**
     * Get RPC ID.
     *
     * @return string|int|null
     */
    public function getRpcId();

    /**
     * Get RPC result.
     *
     * @return mixed
     */
    public function getRpcResult();

    /**
     * Get RPC version.
     */
    public function getRpcVersion(): string;

    /**
     * Get RPC error code.
     */
    public function getRpcErrorCode(): ?int;

    /**
     * Get RPC error message.
     */
    public function getRpcErrorMessage(): ?string;

    /**
     * Get RPC error data.
     *
     * @return mixed
     */
    public function getRpcErrorData();
}
