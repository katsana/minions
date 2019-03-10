<?php

namespace Minions\Server;

use Illuminate\Support\Str;
use Minions\Exceptions\InvalidSignature;
use Minions\Exceptions\InvalidToken;
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
        if (! $this->request->hasHeader('Authorization')) {
            throw new MissingToken();
        } else {
            $header = $this->request->getHeader('Authorization')[0];

            if (Str::startsWith($header, 'Token ')) {
                if (! \hash_equals(Str::substr($header, 6), $this->config['token'])) {
                    throw new InvalidToken();
                }
            }
        }

        return true;
    }

    /**
     * Validate request signature.
     *
     * @return bool
     */
    public function validateRequestSignature(): bool
    {
        if (! $this->request->hasHeader('HTTP_X_SIGNATURE')) {
            throw new MissingSignature();
        } else {
            $header = \explode(',', $this->request->getHeader('HTTP_X_SIGNATURE')[0]);
            $timestamp = \explode('=', $header[0])[1];
            $signature = \explode('=', $header[1])[1];
            $body = (string) $this->request->getBody();

            $expected = \hash_hmac('sha256', "{$timestamp}.{$body}", $this->config['signature']);

            if (! \hash_equals($expected, $this->config['signature'])) {
                throw new InvalidSignature();
            }
        }

        return true;
    }
}
