<?php

namespace Minions\Exceptions;

use Datto\JsonRpc\Exceptions\Exception as JsonRpcException;

class ProjectNotFound extends JsonRpcException
{
    public function __construct(?string $project)
    {
        parent::__construct('Unable to find project: '.($project ?? 'NULL'), -32600);
    }
}
