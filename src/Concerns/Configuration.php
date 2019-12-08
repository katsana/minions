<?php

namespace Minions\Concerns;

use Illuminate\Contracts\Container\Container;
use Minions\Finder;

trait Configuration
{
    /**
     * Use configuration from.
     */
    protected function useConfigurationFrom(Container $app): array
    {
        $config = $app->make('config')->get('minions');

        if (\is_string($config['projects'])) {
            $projects = $app->make($config['projects']);

            if ($projects instanceof Finder) {
                $config['projects'] = $projects;
            }
        }

        return $config;
    }
}
