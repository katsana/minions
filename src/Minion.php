<?php

namespace Minions;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Minions\Client\Minion
 */
class Minion extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'minions.client';
    }
}
