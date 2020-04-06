<?php

namespace Minions\Http;

use Illuminate\Support\Str;
use Laravie\Codex\Security\TimeLimitSignature\Verify;
use Minions\Exceptions\InvalidSignature;
use Minions\Exceptions\InvalidToken;
use Minions\Exceptions\MissingSignature;
use Minions\Exceptions\MissingToken;
use Psr\Http\Message\ServerRequestInterface;

class Message
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
     * The cached request body.
     *
     * @var string
     */
    protected $body;

    /**
     * Construct a new project request.
     */
    public function __construct(string $id, array $config, ServerRequestInterface $request)
    {
        $this->id = $id;
        $this->config = $config;
        $this->request = $request;
        $this->body = (string) $request->getBody();
    }

    /**
     * Get the request instance.
     */
    public function request(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * Get the project id.
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Get the request body.
     */
    public function body(): string
    {
        return $this->body;
    }

    /**
     * Validate request token.
     */
    public function validateRequestToken(): bool
    {
        $projectToken = $this->config['token'] ?? null;

        if (\is_null($projectToken)) {
            return true;
        }

        if (! $this->request->hasHeader('Authorization') || empty($projectToken)) {
            throw new MissingToken();
        } else {
            $header = $this->request->getHeader('Authorization')[0];

            if (Str::startsWith($header, 'Token ')) {
                if (! \hash_equals(Str::substr($header, 6), $projectToken)) {
                    throw new InvalidToken();
                }
            }
        }

        return true;
    }

    /**
     * Validate request signature.
     */
    public function validateRequestSignature(): bool
    {
        $secret = $this->config['signature'] ?? null;
        $body = \json_encode(\json_decode($this->body(), true));

        if (\is_null($secret)) {
            return true;
        }

        if (! $this->request->hasHeader('X-Signature') || empty($secret)) {
            throw new MissingSignature();
        } else {
            $signature = new Verify($secret, 'sha256', $config['signature_expired_in'] ?? 300);

            if (! $signature($body, $this->request->getHeader('X-Signature')[0])) {
                throw new InvalidSignature();
            }
        }

        return true;
    }
}
