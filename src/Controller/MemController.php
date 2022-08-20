<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Controller;

class MemController extends AbstractController
{
    public function index(): array
    {
        $str = shell_exec('more /proc/meminfo');
        $pattern = '/(.+):\\s*([0-9]+)/';
        preg_match_all($pattern, $str, $out);
        $total = $out[2][0]; //物理内存总量
        $used = $out[2][1]; //已使用的内存
        $rate = (100 * ($out[2][0] - $out[2][1]) / $out[2][0]); //内存使用率
        return compact('total', 'used', 'rate');
    }
}
