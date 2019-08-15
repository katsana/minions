<?php

namespace Minions\Concerns;

use Illuminate\Contracts\Container\Container;
use Minions\Projects;

trait Configuration
{
    /**
     * Use configuration from.
     *
     * @param \Illuminate\Contracts\Container\Container $app
     *
     * @return array
     */
    protected function useConfiguration(Container $app): array
    {
        $config = $app->make('config')->get('minions');

        if (\is_string($config['projects'])) {
            $projects = $app->make($config['projects']);

            if ($projects instanceof Projects) {
                $config['projects'] = $projects;
            }
        }

        return $config;
    }
}
