<?php
namespace Mdphp\Region\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CreateTable extends Command
{
    protected $signature = 'mdphp:create-table';

    protected $description = 'create regions table';

    public function handle()
    {
        if ($this->confirm('是否创建 regions 省市区镇行政区域信息表?', true)) {
            $file = __DIR__ . '/../../data/regions.sql';
            if (!File::exists($file)) {
                $this->info('读取 regions.sql 数据文件失败');
                return;
            }

            $this->info('读取 regions.sql 数据文件成功');
            $i = 0;
            $chunk_count = 1;
            $handle = fopen($file, 'r');
            if ($handle) {
                $line = fgets($handle);
                $chunk_content = $line;
                while ($line !== false) {
                    $line = fgets($handle);
                    $chunk_content .= $line;
                    $chunk_count++;
                    if ($chunk_count > 1000) {
                        $this->info($i++ . ': 正在导入 regions.sql 数据文件...');
                        DB::unprepared($chunk_content);
                        $chunk_content = '';
                        $chunk_count = 1;
                    } else {
                        if ($line == false) {
                            $this->info($i++ . ': 正在导入 regions.sql 数据文件...');
                            DB::unprepared($chunk_content);
                        }
                    }
                }
                fclose($handle);
            }
            $this->info('创建 regions 省市区镇行政区域信息表成功');
            return;
        }
        $this->info('已取消创建 regions 省市区镇行政区域信息表');
    }
}