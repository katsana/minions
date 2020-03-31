<?php

namespace Minions\Http;

use ArrayAccess;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use LogicException;
use Minions\Exceptions\Exception;
use ReflectionException;

class Request implements Arrayable, ArrayAccess
{
    /**
     * Request input.
     *
     * @var array
     */
    protected $input = [];

    /**
     * Request message.
     *
     * @var \Minions\Http\Request
     */
    protected $message;

    /**
     * Construct a new request.
     */
    public function __construct(array $input, Message $message)
    {
        $this->input = $input;
        $this->message = $message;
    }

    /**
     * Replicate from a base Request.
     *
     * @return static
     */
    public static function replicateFrom(Request $request, array $keys)
    {
        return new static(
            Arr::only($request->all(), $keys),
            $request->httpMessage()
        );
    }

    /**
     * Get request inputs.
     */
    public function all(): array
    {
        return $this->input;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->all();
    }

    /**
     * Handle resolving the request.
     *
     * @param string|object $resolver
     *
     * @return mixed
     */
    public function handle($resolver)
    {
        $handler = null;

        if (\is_string($resolver)) {
            try {
                $handler = \app($resolver);
            } catch (BindingResolutionException | ReflectionException $e) {
                throw Exception::methodNotFound();
            }
        } elseif (\is_object($resolver)) {
            $handler = $resolver;
        }

        if (\is_null($handler) || ! \is_callable($handler)) {
            throw Exception::methodNotFound();
        } elseif (\method_exists($handler, 'authorize') && $handler->authorize($this) !== true) {
            throw Exception::methodNotFound('Unauthorized request');
        }

        $response = $handler($this);

        if ($response instanceof Arrayable) {
            return $response->toArray();
        }

        return $response;
    }

    /**
     * Forward call to another handler.
     *
     * @param string|object $resolver
     *
     * @return mixed
     */
    public function forwardCallTo($resolver, ?array $parameters = null)
    {
        if (\is_null($parameters)) {
            $parameters = $this->all();
        }

        return (new static(
            $parameters, $this->httpMessage()
        ))->handle($resolver);
    }

    /**
     * Get project Id.
     */
    public function id(): string
    {
        return $this->message->id();
    }

    /**
     * Get HTTP Message.
     */
    public function httpMessage(): Message
    {
        return $this->message;
    }

    /**
     * Determine if the given offset exists.
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->input[$offset]);
    }

    /**
     * Get the value at the given offset.
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->input[$offset] ?? null;
    }

    /**
     * Set the value at the given offset.
     *
     * @param string $offset
     * @param mixed  $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        throw new LogicException('Unable to set value on Request instance.');
    }

    /**
     * Remove the value at the given offset.
     *
     * @param string $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new LogicException('Unable to unset value on Request instance.');
    }

    /**
     * Check if an input element is set on the request.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * Get an input element from the request.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }
}
