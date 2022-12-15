<?php
namespace Mdphp\Region\Services;

class RegionService
{
    public function get()
    {
        $data = [];
        $file = __DIR__ . '/../../data/provinces.json';
        if (is_file($file)) {
            $data = json_decode(file_get_contents($file), true);
        }
        return $data;
    }

    public function find($id)
    {
        $data = [];
        $file = __DIR__ . '/../../data/' . $id . '.json';
        if (is_file($file)) {
            $data = json_decode(file_get_contents($file), true);
        }
        return $data;
    }
}