<?php

namespace Minions;

use ArrayAccess;

abstract class Finder implements ArrayAccess
{
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
     * Determine if the given offset exists.
     *
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
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
        $this->projects[$offset] = $value;
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
        unset($this->projects[$offset]);
    }
}
