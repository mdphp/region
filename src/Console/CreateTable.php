<?php
namespace Mdphp\Region\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Mdphp\Region\Services\CreateTableService;

class CreateTable extends Command
{
    protected $signature = 'mdphp:create-table';

    protected $description = 'create regions table';

    public function handle()
    {
        if ($this->confirm('是否创建 regions 省市区镇行政区域信息表?', true)) {
            app(CreateTableService::class)->create();
            return;
        }
        $this->info('已取消创建 regions 省市区镇行政区域信息表');
    }
}