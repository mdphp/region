<?php
namespace Mdphp\Region\Console;

use Illuminate\Console\Command;
use Mdphp\Region\Services\RegionFetchService;

class RegionFetch extends Command
{
    protected $signature = 'mdphp:fetch {include_town?} {include_city?} {data_path?}';

    protected $description = 'fetch regions data from stats.gov.cn';

    public function handle()
    {
        $include_town = true;
        $include_city = true;
        if ($this->argument('include_town') == 'false') {
            $include_town = false;
        }
        if ($this->argument('include_city') == 'false') {
            $include_city = false;
        }
        $data_path = $this->argument('data_path');
        $st = now();
        $this->info($st->format('Y-m-d H:i:s') . ' 开始下载地区数据');
        $data = (new RegionFetchService(null, $include_town, $include_city, $data_path))->handle();
        $et = now();
        $this->info($et->format('Y-m-d H:i:s') . ' 下载地区数据结束, 用时: ' . $et->diffInMinutes($st) . '分钟');
    }
}