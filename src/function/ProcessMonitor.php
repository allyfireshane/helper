<?php
// +----------------------------------------------------------------------
// | Helper [ I CAN DO MY BEST ]
// +----------------------------------------------------------------------
// | Copyright (c) 2022-2025 http://allyfireshen.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Allyfireshen <allyfireshen@gmail.com>
// +----------------------------------------------------------------------
namespace allyfireshane\helper\function;

/**
 * 进程状态监控静态类
 * 提供内存、CPU、进程状态等系统信息获取功能
 */
class ProcessMonitor
{
    /**
     * 字节数格式化函数
     * @param int $bytes 字节数
     * @param int $decimals 小数位数
     * @return string
     */
    private static function formatBytes(int $bytes, int $decimals): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = intval(value: (strlen($bytes) - 1) / 3);

        if ($bytes == 0) return "0 B";

        // 确保不超过单位数组范围
        $factor = min($factor, values: count($units) - 1);

        return number_format(num: $bytes / pow(num: 1024, exponent: $factor), decimals:  $decimals) . ' ' . $units[$factor];
    }

    /**
     * 获取当前PHP脚本内存使用情况
     * @return array
     */
    public static function getMemoryUsage(): array
    {
        $current = memory_get_usage();
        $peak = memory_get_peak_usage();
        $real = memory_get_usage(real_usage: true);
        $realPeak = memory_get_peak_usage(real_usage: true);
        $decimals = 2;

        return [
            'current_usage' => $current,
            'current_usage_show' => self::formatBytes($current, $decimals),
            'peak_usage' => $peak,
            'peak_usage_show' => self::formatBytes($peak, $decimals),
            'real_usage' => $real,
            'real_usage_show' => self::formatBytes($real, $decimals),
            'real_peak_usage' => $realPeak,
            'real_peak_usage_show' => self::formatBytes($realPeak, $decimals),
        ];
    }

    /**
     * 获取系统CPU使用率
     * @return array
     */
    public static function getCPUUsage(): array
    {
        $loadavg = sys_getloadavg();
        $cpuCount = intval(exec(command: 'nproc 2>/dev/null || echo 1'));

        return [
            'load_1min' => $loadavg[0],
            'load_5min' => $loadavg[1],
            'load_15min' => $loadavg[2],
            'cpu_count' => $cpuCount,
            'usage_percent' => round(num: 100.0 * $loadavg[0] / max(value: 1, values: $cpuCount), precision: 2)
        ];
    }

    /**
     * 获取系统内存信息
     * @return array|string[]
     */
    public static function getSystemMemoryInfo(): array
    {
        if (!file_exists(filename: '/proc/meminfo')) {
            return ['error' => '/proc/meminfo not available'];
        }

        $memoryInfo = [];
        $memInfoContent = file_get_contents(filename: '/proc/meminfo');
        $lines = explode(separator: "\n", string: $memInfoContent);

        foreach ($lines as $line) {
            if (preg_match(pattern: '/^(\w+):\s*(\d+)\s*kB$/', subject: $line, matches: $matches)) {
                $key = strtolower($matches[1]);
                $memoryInfo[$key] = (int)$matches[2] * 1024; // 转换为字节
            }
        }

        $total = $memoryInfo['memtotal'] ?? 0;
        $available = $memoryInfo['memavailable'] ?? 0;
        $free = $memoryInfo['memfree'] ?? 0;
        $cached = $memoryInfo['cached'] ?? 0;
        $buffers = $memoryInfo['buffers'] ?? 0;
        $decimals = 2;

        return [
            'total' => $total,
            'total_show' => self::formatBytes($total, $decimals),
            'available' => $available,
            'available_show' => self::formatBytes($available, $decimals),
            'free' => $free,
            'free_show' => self::formatBytes($free, $decimals),
            'cached' => $cached,
            'cached_show' => self::formatBytes($cached, $decimals),
            'buffers' => $buffers,
            'buffers_show' => self::formatBytes($buffers, $decimals),
        ];
    }

    /**
     * 获取当前进程的详细信息
     * @param int|null $pid 进程ID，默认为当前进程
     * @return array|null[]|string[]
     */
    public static function getProcessInfo(int|null $pid = null): array
    {
        if ($pid === null) {
            $pid = getmypid();
        }

        $procPath = "/proc/{$pid}";
        if (!is_dir($procPath)) {
            return ['error' => "Process {$pid} not found"];
        }

        $info = ['pid' => $pid];
        $decimals = 2;

        // 获取进程状态
        if (file_exists(filename: "{$procPath}/status")) {
            $statusContent = file_get_contents(filename: "{$procPath}/status");

            if (preg_match(pattern: '/^Name:\s*(.*)$/m', subject: $statusContent, matches: $matches)) {
                $info['name'] = trim($matches[1]);
            }

            if (preg_match(pattern: '/^VmRSS:\s*(\d+)\s*kB$/m', subject: $statusContent, matches: $matches)) {
                $rssBytes = intval($matches[1]) * 1024;
                $info['memory_rss'] = $rssBytes;
                $info['memory_rss_show'] = self::formatBytes($rssBytes, $decimals);
            }

            if (preg_match(pattern: '/^VmSize:\s*(\d+)\s*kB$/m', subject: $statusContent, matches: $matches)) {
                $virtualBytes = intval($matches[1]) * 1024;
                $info['memory_virtual'] = $virtualBytes;
                $info['memory_virtual_show'] = self::formatBytes($virtualBytes, $decimals);
            }
            if (preg_match(pattern: '/^Uid:\s*(\d+)/m', subject: $statusContent, matches: $matches)) {
                $info['uid'] = intval($matches[1]);
            }
        }

        // 获取进程运行时间
        if (file_exists(filename: "{$procPath}/stat") && file_exists(filename: '/proc/stat')) {
            $statContent = file_get_contents(filename: "{$procPath}/stat");
            $statFields = explode(separator: ' ', string: $statContent);

            // 获取系统启动时间
            $systemStat = file_get_contents(filename: '/proc/stat');
            preg_match(pattern: '/^btime\s*(\d+)/m', subject: $systemStat, matches: $bTimeMatches);
            $systemBootTime = $bTimeMatches[1] ?? 0;

            if (isset($statFields[21]) && $systemBootTime > 0) {
                $startTimeJiffies = intval($statFields[21]);
                $clockTicks = 100; // 通常为100 jiffies/秒
                $processStartTime = intval(value: $systemBootTime + ($startTimeJiffies / $clockTicks));
                $info['uptime_seconds'] = time() - $processStartTime;
                $info['start_time'] = date(format: 'Y-m-d H:i:s', timestamp: $processStartTime);
            }
        }

        // 获取命令行参数
        if (file_exists(filename: "{$procPath}/cmdline")) {
            $cmdline = file_get_contents(filename: "{$procPath}/cmdline");
            $info['cmdline'] = str_replace(search: "\0", replace: " ", subject: trim($cmdline));
        }

        return $info;
    }

    /**
     * 检查进程是否在运行
     * @param int $pid 进程ID
     * @return bool
     */
    public static function isProcessRunning(int $pid): bool
    {
        if ($pid <= 0) {
            return false;
        }

        // 方法1：检查/proc目录
        if (is_dir(filename: "/proc/{$pid}")) {
            return true;
        }

        // 方法2：使用posix_kill发送0信号检测
        if (function_exists(function: 'posix_kill')) {
            return posix_kill(process_id: $pid, signal: 0);
        }

        return false;
    }

    /**
     * 获取所有PHP进程信息
     * @return array|string[]
     */
    public static function getAllPhpProcesses(): array
    {
        if (!is_dir(filename: '/proc')) {
            return ['error' => '/proc filesystem not available'];
        }

        $phpProcesses = [];
        $items = scandir(directory: '/proc');

        foreach ($items as $item) {
            if (!is_numeric($item)) {
                continue;
            }

            $pid = (int)$item;
            $cmdlinePath = "/proc/{$pid}/cmdline";

            if (!file_exists($cmdlinePath)) {
                continue;
            }

            $cmdline = file_get_contents($cmdlinePath);
            $cmdline = str_replace(search: "\0", replace: " ", subject: $cmdline);

            // 检测是否为PHP进程
            if (str_contains(haystack: $cmdline, needle: 'php')) {
                $processInfo = self::getProcessInfo($pid);
                if (!isset($processInfo['error'])) {
                    $phpProcesses[] = $processInfo;
                }
            }
        }

        return $phpProcesses;
    }

    /**
     * 获取完整的系统状态报告
     * @return array
     */
    public static function getFullStatusReport(): array
    {
        $memoryUsage = self::getMemoryUsage();
        $systemMemory = self::getSystemMemoryInfo();
        $currentProcess = self::getProcessInfo();

        return [
            'timestamp' => date(format: 'Y-m-d H:i:s'),
            'current_pid' => getmypid(),
            'memory' => $memoryUsage,
            'cpu' => self::getCPUUsage(),
            'system_memory' => $systemMemory,
            'current_process' => $currentProcess,
            'php_processes_count' => count(self::getAllPhpProcesses()),
            'is_running' => self::isProcessRunning(getmypid())
        ];
    }

    /**
     * 设置内存限制，返回是否设置成功
     * @param string $limit  内存限制，如 '256M', '1G'
     * @return bool
     */
    public static function setMemoryLimit(string $limit): bool
    {
        return ini_set(option: 'memory_limit', value: $limit) !== false;
    }

    /**
     * 强制执行垃圾回收，返回回收结果
     * @return array
     */
    public static function collectGarbage(): array
    {
        $before = memory_get_usage();
        $cycles = gc_collect_cycles();
        $after = memory_get_usage();
        $freed = $before - $after;

        return [
            'cycles_collected' => $cycles,
            'memory_freed' => $freed,
            'memory_freed_show' => self::formatBytes(bytes: $freed, decimals: 2)
        ];
    }

    /**
     * 获取内存限制信息
     * @return array
     */
    public static function getMemoryLimitInfo(): array
    {
        $limitString = ini_get('memory_limit');
        $limitBytes = self::convertMemoryStringToBytes($limitString);
        $currentUsage = memory_get_usage();
        $usagePercentage = $limitBytes > 0 ? ($currentUsage / $limitBytes) * 100 : 0;

        return [
            'limit_string' => $limitString,
            'limit_bytes' => $limitBytes,
            'limit_show' => $limitBytes == -1 ? '无限制' : self::formatBytes(bytes: $limitBytes, decimals: 2),
            'current_usage_percent' => round(num: $usagePercentage, precision: 2),
            'is_unlimited' => $limitBytes == -1
        ];
    }

    /**
     * 将内存字符串转换为字节数
     * @param string $val 内存字符串，如 '128M', '1G'
     * @return float|int
     */
    private static function convertMemoryStringToBytes(string $val): float|int
    {
        $val = strtolower(trim($val));

        if ($val === '-1') {
            return -1;
        }

        $unit = preg_replace(pattern: '/[^a-z]/', replacement: '', subject: $val);
        $number = preg_replace(pattern: '/[^0-9]/', replacement: '', subject: $val);

        if (!is_numeric($number)) {
            return 0;
        }

        $number = (int)$number;

        return match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => $number,
        };
    }
}