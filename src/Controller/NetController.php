<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Controller;

class NetController extends AbstractController
{
    public function index(): array
    {
        $str = shell_exec('more /proc/net/dev');
        $pattern = '/(eth[0-9]+):\\s*([0-9]+)\\s+([0-9]+)\\s+([0-9]+)\\s+([0-9]+)\\s+([0-9]+)\\s+([0-9]+)\\s+([0-9]+)\\s+([0-9]+)\\s+([0-9]+)\\s+([0-9]+)/';
        preg_match_all($pattern, $str, $out);
        $total = count($out[1]);
        $info = [];
        for ($n = 0; $n < count($out[1]); ++$n) {
            $info[$out[1][$n]] = [
                'receive' => $out[3][$n],
                'send' => $out[11][$n],
            ];
        }
        return compact('total', 'info');
    }
}
