<?php

use Illuminate\Support\Str;
test('user can get provinces data from http request', function() {
    $response = $this->getJson('regions');
    // $response->dd();
    $response->assertStatus(200)
        ->assertJsonPath('error_code', 0)
        ->assertJsonPath('message', 'success')
        ->assertJson(function($json) {
            $json->has('list')
                ->count('list', 31)
                ->etc();
        });
});

test('user can get a region data from http request', function() {
    $parent_id = 4403;
    $id = 440305;
    $name = '南山区';
    $response = $this->getJson('regions/' . $id);
    // $response->dd();
    $response->assertStatus(200)
        ->assertJsonPath('error_code', 0)
        ->assertJsonPath('message', 'success')
        ->assertJson(function($json) use ($id, $parent_id, $name) {
            $json->where('id', $id)
                ->where('code', Str::padRight($id, 12, 0))
                ->where('parent_id', $parent_id)
                ->where('name', $name)
                ->where('level', 2)
                ->has('children')
                ->count('children', 9)
                ->etc();
        });
});