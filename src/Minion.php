<?php

namespace Minions;

use Illuminate\Support\Facades\Facade;

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
