<?php

namespace Minions\Server\Middleware;

use Closure;
use Minions\Server\Project;

class VerifyToken
{
    /**
     * Handle middleware.
     *
     * @param \Minions\Server\Project $project
     * @param \Closure                $next
     *
     * @return mixed
     */
    public function handle(Project $project, Closure $next)
    {
        $project->validateRequestToken();

        return $next($project);
    }
}
