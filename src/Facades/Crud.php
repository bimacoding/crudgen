<?php

namespace Erendi\Crudgenerator\Facades;

use Illuminate\Support\Facades\Facade;

class Crud extends Facade {
    /**
     * Return facade accessor
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'erendicrud';
    }
}
