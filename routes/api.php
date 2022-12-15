<?php

use Illuminate\Support\Facades\Route;
use Mdphp\Region\Http\Controllers\RegionController;

Route::get('regions', [RegionController::class, 'index']);
Route::get('regions/{id}', [RegionController::class, 'show']);