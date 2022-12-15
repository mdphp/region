<?php

test('user can get provinces data from Region facade or RegionService', function() {
    $provinces = \Mdphp\Region\Facades\Region::get();
    $this->assertCount(31, $provinces);
});

test('user can get a region data from Region facade or RegionService', function() {
    $data = \Mdphp\Region\Facades\Region::find(440305);
    $this->assertCount(6, $data);
});