<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

test('user can fetch regions data from stats.gov.cn', function() {
    $data_path =  __DIR__ . '/../../test_data/';
    Artisan::call('mdphp:fetch', [
        'include_town' => 'true',
        'include_city' => 'false',
        'data_path' => $data_path
    ]);
    $this->assertTrue(File::exists($data_path . 'provinces.json'));
    $data = \Mdphp\Region\Facades\Region::get();
    $this->assertCount(31, $data);
});

