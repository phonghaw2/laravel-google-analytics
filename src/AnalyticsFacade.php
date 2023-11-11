<?php

namespace Phonghaw2\Analytics;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Phonghaw2\Analytics\Analytics
 */
class AnalyticsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-analytics';
    }
}
