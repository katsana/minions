<?php

namespace Minions\Server;

use Psr\Http\Message\ServerRequestInterface;

class Project
{
    /**
     * Project ID.
     *
     * @var string
     */
    protected $id;

    /**
     * Project configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The PSR-7 Request.
     *
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected $request;

    /**
     * Construct a new project request.
     *
     * @param string                                   $id
     * @param array                                    $config
     * @param \Psr\Http\Message\ServerRequestInterface $request
     */
    public function __construct(string $id, array $config, ServerRequestInterface $request)
    {
        $this->id = $id;
        $this->config = $config;
        $this->request = $request;
    }

    /**
     * Get the request instance.
     *
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function request(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * Validate request token.
     *
     * @return bool
     */
    public function validateRequestToken(): bool
    {
        return true;
    }

    /**
     * Validate request signature.
     *
     * @return bool
     */
    public function validateRequestSignature(): bool
    {
        return true;
    }
}
