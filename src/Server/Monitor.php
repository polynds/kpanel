<?php

declare(strict_types=1);
/**
 * happy coding!!!
 */
namespace Polynds\KPanel\Server;

class Monitor
{
    public function get_remote_addr()
    {
//        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
//            return $_SERVER['HTTP_X_REAL_IP'];
//        }
//        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//            return preg_replace('/^.+,\s*/', '', $_SERVER['HTTP_X_FORWARDED_FOR']);
//        }
//
//        return $_SERVER['REMOTE_ADDR'];
        return '';
    }

    public function get_server_addr()
    {
//        if ($_SERVER['SERVER_ADDR'] != '127.0.0.1') {
//            return $_SERVER['SERVER_ADDR'];
//        }

        return gethostbyname(php_uname('n'));
    }

    public function get_stat()
    {
        $content = file('/proc/stat');
        $array = array_shift($content);
        $array = preg_split('/\s+/', trim($array));
        $stat = array_slice($array, 1);
        $total = array_sum($stat);
        return [
            'total' => $total,
            'user' => $stat[0],
            'nice' => $stat[1],
            'sys' => $stat[2],
            'idle' => $stat[3],
            'iowait' => $stat[4],
            'irq' => $stat[5],
            'softirq' => $stat[6],
            'steal' => $stat[7],
            'UserBar' => $stat[0] + $stat[1],
            'SystemBar' => $total - $stat[0] - $stat[1] - $stat[3],
        ];
    }

    public function get_sockstat(): array
    {
        $info = [];

        $content = file('/proc/net/sockstat');
        foreach ($content as $line) {
            $parts = explode(':', $line);
            $key = trim($parts[0]);
            $values = preg_split('/\s+/', trim($parts[1]));
            $info[$key] = [];
            for ($i = 0; $i < count($info); $i += 2) {
                if (! isset($values[$i])) {
                    continue;
                }
                $info[$key][$values[$i]] = $values[$i + 1];
            }
        }

        return $info;
    }

    public function get_cpuinfo()
    {
        $info = [];

        if (! ($str = @file('/proc/cpuinfo'))) {
            return false;
        }

        $str = implode('', $str);
        @preg_match_all("/processor\\s{0,}\\:+\\s{0,}([\\w\\s\\)\\(\\@.-]+)([\r\n]+)/s", $str, $processor);
        @preg_match_all("/model\\s+name\\s{0,}\\:+\\s{0,}([\\w\\s\\)\\(\\@.-]+)([\r\n]+)/s", $str, $model);

        if (count($model[0]) == 0) {
            @preg_match_all("/Hardware\\s{0,}\\:+\\s{0,}([\\w\\s\\)\\(\\@.-]+)([\r\n]+)/s", $str, $model);
        }
        @preg_match_all("/cpu\\s+MHz\\s{0,}\\:+\\s{0,}([\\d\\.]+)[\r\n]+/", $str, $mhz);

        if (count($mhz[0]) == 0) {
            $values = @file('/sys/devices/system/cpu/cpu0/cpufreq/cpuinfo_max_freq');
            $mhz = ['', [sprintf('%.3f', intval($values[0]) / 1000)]];
        }

        @preg_match_all("/cache\\s+size\\s{0,}\\:+\\s{0,}([\\d\\.]+\\s{0,}[A-Z]+[\r\n]+)/", $str, $cache);
        @preg_match_all("/(?i)bogomips\\s{0,}\\:+\\s{0,}([\\d\\.]+)[\r\n]+/", $str, $bogomips);
        @preg_match_all("/(?i)(flags|Features)\\s{0,}\\:+\\s{0,}(.+)[\r\n]+/", $str, $flags);

//        $lang = (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) === 'zh');

        if (is_array($model[1])) {
            $info['num'] = sizeof($processor[1]);
            $info['model'] = $model[1][0];
            $info['frequency'] = $mhz[1][0];
            $info['bogomips'] = $bogomips[1][0];
            if (count($cache[0]) > 0) {
                $info['l2cache'] = trim($cache[1][0]);
            }
            $info['flags'] = $flags[2][0];
        }

        return $info;
    }

    public function get_uptime()
    {
        if (! ($str = @file('/proc/uptime'))) {
            return false;
        }

//        $lang = (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) === 'zh');
        $lang = 'zh';

        $uptime = '';
        $str = explode(' ', implode('', $str));
        $str = trim($str[0]);
        $min = $str / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        $duint = ! $lang ? (' day' . ($days > 1 ? 's ' : ' ')) : '天';
        $huint = ! $lang ? (' hour' . ($hours > 1 ? 's ' : ' ')) : '小时';
        $muint = ! $lang ? (' minute' . ($min > 1 ? 's ' : ' ')) : '分钟';

        if ($days !== 0) {
            $uptime = $days . $duint;
        }
        if ($hours !== 0) {
            $uptime .= $hours . $huint;
        }
        $uptime .= $min . $muint;

        return $uptime;
    }

    public function get_tempinfo(): array
    {
        $info = [];

        if ($str = @file('/sys/class/thermal/thermal_zone0/temp')) {
            $info['cpu'] = $str[0] / 1000.0;
        }

        # if ($str = @file('/sys/class/thermal/thermal_zone10/temp'))
        #	$info['gpu'] = $str[0]/1000.0;

        return $info;
    }

    public function get_meminfo()
    {
        $info = [];

        if (! ($str = @file('/proc/meminfo'))) {
            return false;
        }

        $str = implode('', $str);
        preg_match_all('/MemTotal\\s{0,}\\:+\\s{0,}([\\d\\.]+).+?MemFree\\s{0,}\\:+\\s{0,}([\\d\\.]+).+?Cached\\s{0,}\\:+\\s{0,}([\\d\\.]+).+?SwapTotal\\s{0,}\\:+\\s{0,}([\\d\\.]+).+?SwapFree\\s{0,}\\:+\\s{0,}([\\d\\.]+)/s', $str, $buf);
        preg_match_all('/Buffers\\s{0,}\\:+\\s{0,}([\\d\\.]+)/s', $str, $buffers);

        $info['memTotal'] = round($buf[1][0] / 1024, 2);
        $info['memFree'] = round($buf[2][0] / 1024, 2);
        $info['memBuffers'] = round($buffers[1][0] / 1024, 2);
        $info['memCached'] = round($buf[3][0] / 1024, 2);
        $info['memUsed'] = round($info['memTotal'] - $info['memFree'] - $info['memBuffers'] - $info['memCached'], 2);
        $info['memUsedPercent'] = (floatval($info['memTotal']) != 0) ? round($info['memUsed'] / $info['memTotal'] * 100, 2) : 0;
        $info['memBuffersPercent'] = (floatval($info['memTotal']) != 0) ? round($info['memBuffers'] / $info['memTotal'] * 100, 2) : 0;
        $info['memCachedPercent'] = (floatval($info['memTotal']) != 0) ? round($info['memCached'] / $info['memTotal'] * 100, 2) : 0;

        $info['swapTotal'] = round($buf[4][0] / 1024, 2);
        $info['swapFree'] = round($buf[5][0] / 1024, 2);
        $info['swapUsed'] = round($info['swapTotal'] - $info['swapFree'], 2);
        $info['swapPercent'] = (floatval($info['swapTotal']) != 0) ? round($info['swapUsed'] / $info['swapTotal'] * 100, 2) : 0;

        foreach ($info as $key => $value) {
            if (strpos($key, 'Percent') > 0) {
                continue;
            }
            if ($value < 1024) {
                $info[$key] .= ' M';
            } else {
                $info[$key] = round($value / 1024, 3) . ' G';
            }
        }

        return $info;
    }

    public function get_loadavg()
    {
        if (! ($str = @file('/proc/loadavg'))) {
            return false;
        }

        $str = explode(' ', implode('', $str));
        $str = array_chunk($str, 4);
        return implode(' ', $str[0]);
    }

    public function get_distname()
    {
        foreach (glob('/etc/*release') as $name) {
            if ($name == '/etc/centos-release' || $name == '/etc/redhat-release' || $name == '/etc/system-release') {
                $os_name = file($name);
                return array_shift($os_name);
            }

            $release_info = @parse_ini_file($name);

            if (isset($release_info['DISTRIB_DESCRIPTION'])) {
                return $release_info['DISTRIB_DESCRIPTION'];
            }

            if (isset($release_info['PRETTY_NAME'])) {
                return $release_info['PRETTY_NAME'];
            }
        }

        return php_uname('s') . ' ' . php_uname('r');
    }

    public function get_boardinfo(): array
    {
        $info = [];

        if (is_file('/sys/class/dmi/id/bios_vendor')) {
            $bios_vendor = file('/sys/class/dmi/id/bios_vendor', FILE_IGNORE_NEW_LINES);
            $info['BIOSVendor'] = array_shift($bios_vendor);
            $bios_version = file('/sys/class/dmi/id/bios_version', FILE_IGNORE_NEW_LINES);
            $info['BIOSVersion'] = array_shift($bios_version);
            $bios_date = file('/sys/class/dmi/id/bios_date', FILE_IGNORE_NEW_LINES);
            $info['BIOSDate'] = array_shift($bios_date);
        }

        if (is_file('/sys/class/dmi/id/board_name')) {
            $board_vendor = file('/sys/class/dmi/id/board_vendor', FILE_IGNORE_NEW_LINES);
            $info['boardVendor'] = array_shift($board_vendor);
            $board_name = file('/sys/class/dmi/id/board_name', FILE_IGNORE_NEW_LINES);
            $info['boardName'] = array_shift($board_name);
            $board_version = file('/sys/class/dmi/id/board_version', FILE_IGNORE_NEW_LINES);
            $info['boardVersion'] = array_shift($board_version);
        } elseif (is_file('/sys/class/dmi/id/product_name')) {
            $product_name = file('/sys/class/dmi/id/product_name', FILE_IGNORE_NEW_LINES);
            $info['boardVendor'] = array_shift($product_name);
            $info['boardName'] = '';
            $info['boardVersion'] = '';
        }

        if (is_dir('/dev/disk/by-id')) {
            if ($names = array_filter(scandir('/dev/disk/by-id'), function ($k) {
                return $k[0] != '.' && strpos($k, 'DVD-ROM') === false;
            })) {
                $parts = explode('_', array_shift($names));
                $parts = explode('-', array_shift($parts), 2);
                $info['diskVendor'] = strtoupper($parts[0]);
                $info['diskModel'] = $parts[1];
            }
        }

        return $info;
    }

    public function get_diskinfo(): array
    {
        $info = [];

        $info['diskTotal'] = round(@disk_total_space('.') / (1024 * 1024 * 1024), 2);
        $info['diskFree'] = round(@disk_free_space('.') / (1024 * 1024 * 1024), 2);
        $info['diskUsed'] = round($info['diskTotal'] - $info['diskFree'], 2);
        $info['diskPercent'] = 0;
        if (floatval($info['diskTotal']) != 0) {
            $info['diskPercent'] = round($info['diskUsed'] / $info['diskTotal'] * 100, 2);
        }

        return $info;
    }

    public function get_netdev(): array
    {
        $info = [];

        $strs = @file('/proc/net/dev');
        for ($i = 2; $i < count($strs); ++$i) {
            $parts = preg_split('/\s+/', trim($strs[$i]));
            $dev = trim($parts[0], ':');
            $info[$dev] = [
                'rx' => intval($parts[1]),
                'human_rx' => $this->human_filesize($parts[1]),
                'tx' => intval($parts[9]),
                'human_tx' => $this->human_filesize($parts[9]),
            ];
        }

        return $info;
    }

    public function get_netarp(): array
    {
        $info = [];

        $seen = [];
        $strs = @file('/proc/net/arp');
        for ($i = 1; $i < count($strs); ++$i) {
            $parts = preg_split('/\s+/', $strs[$i]);
            if ($parts[2] == '0x2' && ! isset($seen[$parts[3]])) {
                $seen[$parts[3]] = true;
                $info[$parts[0]] = [
                    'hw_type' => $parts[1] == '0x1' ? 'ether' : $parts[1],
                    'hw_addr' => $parts[3],
                    'device' => $parts[5],
                ];
            }
        }

        return $info;
    }

    public function toArray(): array
    {
        return [
            'remote_addr' => $this->get_remote_addr(),
            'server_addr' => $this->get_server_addr(),
            'stat' => $this->get_stat(),
            'sockstat' => $this->get_sockstat(),
            'cpuinfo' => $this->get_cpuinfo(),
            'uptime' => $this->get_uptime(),
            'tempinfo' => $this->get_tempinfo(),
            'meminfo' => $this->get_meminfo(),
            'loadavg' => $this->get_loadavg(),
            'distname' => $this->get_distname(),
            'boardinfo' => $this->get_boardinfo(),
            'diskinfo' => $this->get_diskinfo(),
            'netdev' => $this->get_netdev(),
            'netarp' => $this->get_netarp(),
        ];
    }

    public function html(): string
    {
        return $this->listHtml($this->toArray());
    }

    protected function human_filesize($bytes): string
    {
        if ($bytes == 0) {
            return '0 B';
        }

        $units = ['B', 'K', 'M', 'G', 'T'];
        $size = '';

        while ($bytes > 0 && count($units) > 0) {
            $size = strval($bytes % 1024) . ' ' . array_shift($units) . ' ' . $size;
            $bytes = intval($bytes / 1024);
        }

        return $size;
    }

    private function listHtml(array $data): string
    {
        $html = '<ul>';
        foreach ($data as $name => $quota) {
            if (is_array($quota)) {
                $html .= $this->listHtml($quota);
            } else {
                $html .= "<li>{$name}=>{$quota}</li>";
            }
        }
        $html .= '</ul>';
        return $html;
    }
}
