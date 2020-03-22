<?php

namespace Minions;

use ArrayAccess;
use Illuminate\Support\Arr;

abstract class Finder implements ArrayAccess
{
    /**
     * Indicates if the finder has "booted".
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * List of projects.
     *
     * @var array
     */
    protected $projects = [];

    /**
     * Register new project.
     *
     * @return $this
     */
    final public function register(
        string $name,
        string $token,
        string $signature,
        ?string $endpoint = null,
        array $options = []
    ): self {
        $this->projects[$name] = \compact('endpoint', 'token', 'signature', 'options');

        return $this;
    }

    /**
     * Boot the finder.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Check if the finder needs to be booted and if so, do it.
     */
    protected function bootIfNotBooted(): void
    {
        if (! $this->booted) {
            $this->boot();
        }

        $this->booted = true;
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
        $this->bootIfNotBooted();

        return isset($this->projects[$offset]);
    }

    /**
     * Get the value for a given offset.
     *
     * @param string $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        $this->bootIfNotBooted();

        return $this->projects[$offset];
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
        $this->projects[$offset] = \array_merge([
            'endpoint' => null,
            'options' => [],
        ], Arr::only($value, ['endpoint', 'token', 'signature', 'options']));
    }

    /**
     * Unset the value at the given offset.
     *
     * @param string $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->bootIfNotBooted();

        unset($this->projects[$offset]);
    }
}
