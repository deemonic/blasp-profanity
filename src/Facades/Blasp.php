<?php

namespace Blaspsoft\Blasp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Blaspsoft\Blasp\Skeleton\SkeletonClass
 */
class Blasp extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'blasp';
    }
}
