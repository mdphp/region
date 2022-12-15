<?php
namespace Mdphp\Region\Http\Controllers;

use Mdphp\Region\Facades\Region;
use Illuminate\Routing\Controller;
use Mdphp\Region\Services\RegionService;
use Mdphp\Region\Http\Resources\BaseResource;
use Mdphp\Region\Http\Resources\BaseCollection;

class RegionController  extends Controller
{
    public function index()
    {
        $provinces = Region::get();
        return new BaseCollection(['list' => $provinces]);
    }

    public function show($id)
    {
        $region = Region::find($id);
        return new BaseResource($region);
    }
}