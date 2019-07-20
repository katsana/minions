<?php

namespace Minions\Server;

use Datto\JsonRpc\Evaluator as DattoEvaluator;
use Datto\JsonRpc\Exceptions\MethodException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use ReflectionException;

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
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @param array                                     $services
     * @param \Minions\Server\Message                   $message
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
            throw new MethodException();
        }

        try {
            $handler = $this->container->make($resolver);
        } catch (BindingResolutionException | ReflectionException $e) {
            throw new MethodException();
        }

        return $handler($arguments, $this->message);
    }

    /**
     * Find resolver based on services.
     *
     * @param string $key
     *
     * @return string|null
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
