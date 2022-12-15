<?php
namespace Mdphp\Region;

use Mdphp\Region\Console\CreateTable;
use Mdphp\Region\Console\RegionFetch;
use Illuminate\Support\ServiceProvider;
use Mdphp\Region\Services\RegionService;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        JsonResource::withoutWrapping();

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/region.php',
            'region'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                RegionFetch::class,
                CreateTable::class,
            ]);
        }
    }

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/region.php' => config_path('region.php'),
            ], 'region');
        }

        $this->app->bind('region', function($app) {
            return new RegionService();
        });
    }
}