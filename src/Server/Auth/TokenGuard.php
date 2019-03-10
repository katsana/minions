<?php

namespace Minions\Server\Auth;

use Illuminate\Http\Request;

class TokenGuard implements Auth\Contracts\Handler
{
    /**
     * Illuminate authentication manager.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The guard driver name.
     *
     * @var string
     */
    protected $guard = 'api';

    /**
     * Construct Token based Guard for Json-RPC.
     *
     * @param \Illuminate\Auth\AuthManager $auth
     * @param \Illuminate\Http\Request     $request
     */
    public function __construct(AuthManager $auth, Request $request)
    {
        $this->auth = $auth->guard($this->guard);
        $this->request = $request;
    }

    /**
     * Determines if this handler is capable of authorizing this request.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return bool
     */
    public function canHandle(string $method, array $arguments): bool
    {
        return true;
    }

    /**
     * Determines if this request is actually authenticated.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return bool
     */
    public function authenticate(string $method, array $arguments): bool
    {
        return $this->auth->user();
    }
}
