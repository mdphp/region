<?php
namespace Mdphp\Region\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CreateTableService
{
    public function create()
    {
        $file = __DIR__ . '/../../data/regions.sql';
        if (!File::exists($file)) {
            dump('读取 regions.sql 数据文件失败');
            return;
        }

        dump('读取 regions.sql 数据文件成功');
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
                    dump($i++ . ': 正在导入 regions.sql 数据文件...');
                    DB::unprepared($chunk_content);
                    $chunk_content = '';
                    $chunk_count = 1;
                } else {
                    if ($line == false) {
                        dump($i++ . ': 正在导入 regions.sql 数据文件...');
                        DB::unprepared($chunk_content);
                    }
                }
            }
            fclose($handle);
        }
        dump('创建 regions 省市区镇行政区域信息表成功');
        return;

    }
}
