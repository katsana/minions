<?php

namespace Minions;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;

class Configuration extends Repository
{
    /**
     * Make configuration for Minions.
     *
     * @return static
     */
    public static function make(Container $app)
    {
        $config = $app->make('config')->get('minions', [
            'id' => 'minion-server',
            'projects' => [],
        ]);

        if (\is_string($config['projects'])) {
            $projects = $app->make($config['projects']);

            if ($projects instanceof Finder) {
                $config['projects'] = $projects;
            }
        }

        return new static($config);
    }
}
