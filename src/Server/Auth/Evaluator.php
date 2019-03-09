<?php

namespace Minions\Server\Auth;

use Datto\JsonRpc\Evaluator as EvaluatorContract;

/**
 * Implementation of the JsonRpc\Evaluator with pre-execution authentication/authorization.
 *
 * This class wraps around an existing evaluator, and only executes/evaluates a request
 * if the Authenticator allows it.
 *
 * @author Philipp Heckel <ph@datto.com>
 */
class Evaluator implements EvaluatorContract
{
    /** @var \JsonRpc\Evaluator */
    private $evaluator;

    /** @var \Minions\Server\Auth\Authenticator */
    private $authenticator;

    /**
     * Creates an evaluator instance using the given Authenticator.
     *
     * @param \JsonRpc\Evaluator $evaluator
     * @param \Minions\Server\Auth\Authenticator $authenticator
     */
    public function __construct(Evaluator $evaluator, Authenticator $authenticator)
    {
        $this->evaluator = $evaluator;
        $this->authenticator = $authenticator;
    }

    /**
     * Authenticate request and (if successful) map method name to callable
     * and run it with the given arguments.
     *
     * @param string $method Method name
     * @param array $arguments Positional or associative argument array
     *
     * @return mixed Return value of the callable
     *
     * @throws \Minions\Server\Exceptions\MissingAuthentication If the no credentials are given
     * @throws \Minions\Server\Exceptions\InvalidAuthentication If the given credentials are invalid
     */
    public function evaluate($method, $arguments)
    {
        $this->authenticator->authenticate($method, $arguments);

        return $this->evaluator->evaluate($method, $arguments);
    }
}
