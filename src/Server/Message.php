<?php

namespace Minions\Server;

use Carbon\Carbon;
use Illuminate\Support\Str;
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
        $this->body = (string) $request->getBody();
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
     * Get the request body.
     *
     * @return string
     */
    public function body(): string
    {
        return $this->body;
    }

    /**
     * Validate request token.
     *
     * @return bool
     */
    public function validateRequestToken(): bool
    {
        $projectToken = $this->config['token'] ?? null;

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
     *
     * @return bool
     */
    public function validateRequestSignature(): bool
    {
        $projectSignature = $this->config['signature'] ?? null;

        if (! $this->request->hasHeader('HTTP_X_SIGNATURE') || empty($projectSignature)) {
            throw new MissingSignature();
        } else {
            $header = \explode(',', $this->request->getHeader('HTTP_X_SIGNATURE')[0]);
            $timestamp = \explode('=', $header[0])[1];
            $signature = \explode('=', $header[1])[1];

            $expiry = Carbon::createFromTimestamp($timestamp)
                            ->addSeconds($config['signature_expired_in'] ?? 300);

            $body = \json_encode(\json_decode($this->body(), true));

            $expected = \hash_hmac('sha256', "{$timestamp}.{$body}", $projectSignature);

            if (! \hash_equals($expected, $signature) || Carbon::now()->gte($expiry)) {
                throw new InvalidSignature();
            }
        }

        return true;
    }
}
