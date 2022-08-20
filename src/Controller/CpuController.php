<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Controller;

class CpuController extends AbstractController
{
    public function index(): array
    {
        $str = shell_exec('more /proc/stat');
        $pattern = '/(cpu[0-9]?)[\\s]+([0-9]+)[\\s]+([0-9]+)[\\s]+([0-9]+)[\\s]+([0-9]+)[\\s]+([0-9]+)[\\s]+([0-9]+)[\\s]+([0-9]+)/';
        preg_match_all($pattern, $str, $out);
        $total = count($out[1]);
        $info = [];
        for ($n = 0; $n < count($out[1]); ++$n) {
            $info[$out[1][$n]] = (100 * ($out[2][$n] + $out[3][$n]) / ($out[4][$n] + $out[5][$n] + $out[6][$n] + $out[7][$n])) . '%';
        }
        return compact('total', 'info');
    }
}
