<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

test('[!not for test!] fetch all data from from stats.gov.cn', function() {
    Artisan::call('mdphp:fetch');
    $this->assertTrue(File::exists(__DIR__ . '/../../data/provinces.json'));
});