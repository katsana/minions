<?php

namespace Minions\Client;

interface ResponseInterface
{
    /**
     * @return string|int|null
     */
    public function getRpcId();

    /**
     * @return mixed
     */
    public function getRpcResult();

    public function getRpcVersion(): string;

    public function getRpcErrorCode(): ?int;

    public function getRpcErrorMessage(): ?string;

    /**
     * @return mixed
     */
    public function getRpcErrorData();
}
