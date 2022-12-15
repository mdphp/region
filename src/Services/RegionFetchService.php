<?php
namespace Mdphp\Region\Services;

use Exception;
use Goutte\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class RegionFetchService
{
    public $url = 'http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/2021/'; // 国家统计局省市区镇行政区域
    public $include_district = true;
    public $include_city = true;
    public $data_path = __DIR__ . '/../../data/';

    public function __construct($url = '', $include_district = true, $include_city = true, $data_path = '')
    {
        $this->url = Config::get('region.url');
        if (!empty($url)) {
            $this->url = $url;
        }
        $this->include_district = $include_district;
        $this->include_city = $include_city;
        if (!empty($data_path) && is_dir($data_path)) {
            $this->data_path = $data_path;
        }
    }

    public function handle()
    {
        $res = $this->fetchData($this->url);
        $provinces = [];
        $provinces_json = [];
        $res->filter('tr.provincetr a')->each(function ($node) use (&$provinces, &$provinces_json) {
            $code = (int)trim($node->attr('href'), '.html');
            $next_url = $this->url . $node->attr('href');
            $data = array(
                'id' => (int)$code,
                'parent_id' => null,
                'code' => Str::padRight($code, 12, 0),
                'name' => $node->text(),
                'level' => 0,
                'children' => []
            );
            $provinces_json[] = $data;
            if ($this->include_city || (int)$code == 44) {
                $data['children'] = $this->getCity($next_url, $code);
            }
            $provinces[] = $data;
            file_put_contents($this->data_path . $code . '.json', json_encode($data));
        });
        file_put_contents($this->data_path . 'provinces.json', json_encode($provinces_json));
        file_put_contents($this->data_path . 'regions.json', json_encode($provinces));
        return $provinces;
    }

    public function getCity($url, $province_code)
    {
        $res = $this->fetchData($url);
        $cities = [];
        $res->filter('tr.citytr')->each(function ($node) use (&$cities, $province_code) {
            $first_node = $node->filter('a')->first();
            $last_node = $node->filter('a')->last();
            $code = (int)trim($first_node->attr('href'), '.html');
            $code_string = trim($first_node->attr('href'), '.html'); // 11/1101
            $code_string = explode('/', $code_string);
            if (Arr::get($code_string, 1)) {
                $code_string = Arr::get($code_string, 1);
            }
            $next_url = $this->url . $first_node->attr('href');
            $data = array(
                'id' => (int)$code_string,
                'parent_id' => (int)$province_code,
                'code' => Str::padRight($code_string, 12, 0),
                'name' => $last_node->text(),
                'level' => 1,
            );
            if ($this->include_city || (int)$code_string == 4403) {
                $data['children'] = $this->getTown($next_url, $code, $code_string);
            }
            $cities[] = $data;
            file_put_contents($this->data_path .$code_string . '.json', json_encode($data));
        });
        return $cities;
    }

    public function getTown($url, $city_code, $code_string)
    {
        $res = $this->fetchData($url);
        $towns = [];
        $url = $this->url . $city_code;

        $res->filter('tr.countytr')->each(function ($node) use (&$towns, $url, $code_string) {
            $next_url = '';
            $first_node = $node->filter('a')->first();
            $last_node = $node->filter('a')->last();
            if ($first_node->count() > 0) {
                $next_url = $url . '/' . $first_node->attr('href');
            } else {
                $first_node = $node->filter('td')->first();
                $last_node = $node->filter('td')->last();
            }
            $code = (int)trim($first_node->text(), '0');
            $data = array(
                'id' => (int)$code,
                'parent_id' => (int)$code_string,
                'code' => Str::padRight($code, 12, 0),
                'name' => $last_node->text(),
                'level' => 2,
            );
            if ($this->include_district) {
                $data['children'] = $this->getDistrict($next_url, $code);
            }
            $towns[] = $data;
            file_put_contents($this->data_path .$code . '.json', json_encode($data));
        });

        // 东莞市没有区，直接到镇
        $res->filter('tr.towntr')->each(function ($node) use (&$towns, $code_string) {
            $first_node = $node->filter('a')->first();
            $last_node = $node->filter('a')->last();
            if ($first_node->count() <= 0) {
                $first_node = $node->filter('td')->first();
                $last_node = $node->filter('td')->last();
            }
            $code = (int)trim($first_node->text(), '0');
            $data = array(
                'id' => (int)$code,
                'parent_id' => (int)$code_string,
                'code' => Str::padRight($code, 12, 0),
                'name' => $last_node->text(),
                'level' => 3,
                'children' => []
            );
            $towns[] = $data;
        });
        return $towns;
    }

    public function getDistrict($url, $town_code)
    {
        if (empty($url)) {
            return [];
        }
        $res = $this->fetchData($url);
        $districts = [];
        $res->filter('tr.towntr')->each(function ($node) use (&$districts, $town_code) {
            $first_node = $node->filter('a')->first();
            $last_node = $node->filter('a')->last();
            if ($first_node->count() <= 0) {
                $first_node = $node->filter('td')->first();
                $last_node = $node->filter('td')->last();
            }
            $code = (int)trim($first_node->text(), '0');
            $data = array(
                'id' => (int)$code,
                'parent_id' => (int)$town_code,
                'code' => Str::padRight($code, 12, 0),
                'name' => $last_node->text(),
                'level' => 3,
                'children' => []
            );
            $districts[] = $data;
        });
        return $districts;
    }

    public function parseUrl($url)
    {
        return preg_replace('/(\/index|\d+)\.html$/', '/', $url);
    }

    public function fetchData($url, $count = 1)
    {
        $secs = rand(1, 5);
        // dump($count . ' random delay: ' . $secs);
        sleep($secs);
        dump($url);
        try {
            $client = new Client();
            $res = $client->request('GET', $url);
        } catch (Exception $e) {
            if ($count > 5) {
                dd('重试 ' . $count . ' 次失败');
            }
            $res = $this->fetchData($url, $count++);
        }
        return $res;
    }
}
