<?php
namespace Mdphp\Region\Facades;

use Illuminate\Support\Facades\Facade;

class Region extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'region';
    }
}