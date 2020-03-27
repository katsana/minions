<?php

namespace Minions\Concerns;

use Illuminate\Contracts\Container\Container;

/**
 * @deprecated v1.6.0 and will be removed on v3.0.0.
 */
trait Configuration
{
    /**
     * Use configuration from.
     */
    protected function useConfigurationFrom(Container $app): array
    {
        return \Minions\Configuration::make($app)->all();
    }
}
