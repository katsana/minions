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

    /**
     * @return string
     */
    public function getRpcVersion(): string;

    /**
     * @return int|null
     */
    public function getRpcErrorCode(): ?int;

    /**
     * @return string|null
     */
    public function getRpcErrorMessage(): ?string;

    /**
     * @return mixed
     */
    public function getRpcErrorData();
}
