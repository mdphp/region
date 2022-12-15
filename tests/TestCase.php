<?php
namespace Mdphp\Region\Tests;

use Mdphp\Region\RegionServiceProvider;


class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [RegionServiceProvider::class];
    }

}