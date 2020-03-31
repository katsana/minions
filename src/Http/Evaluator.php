<?php

namespace Minions\Http;

use Datto\JsonRpc\Evaluator as DattoEvaluator;
use Illuminate\Contracts\Container\Container;
use Minions\Exceptions\Exception;

class Evaluator implements DattoEvaluator
{
    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * List of services.
     *
     * @var array
     */
    protected $services = [];

    /**
     * The message.
     *
     * @var \Minions\Server\Message
     */
    protected $message;

    /**
     * Construct a new Evaluator.
     */
    public function __construct(Container $container, array $services, Message $message)
    {
        $this->container = $container;
        $this->services = $services;
        $this->message = $message;
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function evaluate($method, $arguments)
    {
        if (\is_null($resolver = $this->findResolver($method))) {
            throw Exception::methodNotFound();
        }

        $request = new Request($arguments, $this->message);

        return $request->handle($resolver);
    }

    /**
     * Find resolver based on services.
     */
    protected function findResolver(string $key): ?string
    {
        if (! \array_key_exists($key, $this->services)) {
            return null;
        }

        $resolver = $this->services[$key];

        if (\is_string($resolver)) {
            return $resolver;
        }

        if (\is_array($resolver) && \array_key_exists('handler', $resolver)) {
            if (! \array_key_exists('projects', $resolver)) {
                $resolver['projects'] = ['*'];
            }

            if ($resolver['projects'] === ['*'] || \in_array($this->message->id(), $resolver['projects'])) {
                return $resolver['handler'];
            }
        }

        return null;
    }
}
