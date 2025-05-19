<?php

namespace DeepSeek\DeepseekLaravel;

use Illuminate\Support\Facades\Facade;

class DeepseekLaravelFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'deepseek-laravel';
    }
}
