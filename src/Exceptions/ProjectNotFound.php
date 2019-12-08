<?php

namespace Minions\Exceptions;

class ProjectNotFound extends Exception
{
    /**
     * Construct project not found exception.
     */
    public function __construct(?string $project)
    {
        parent::__construct('Unable to find project: '.($project ?? 'NULL'), -32600);
    }
}
