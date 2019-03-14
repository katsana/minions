<?php

namespace Minions\Exceptions;

class ProjectNotFound extends Exception
{
    public function __construct(?string $project)
    {
        parent::__construct('Unable to find project: '.($project ?? 'NULL'), -32600);
    }
}
