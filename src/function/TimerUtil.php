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

use DateInterval;
use DateTime;
use Exception;

class TimerUtil
{
    /**
     * 返回今日开始和结束的时间戳
     * @return array
     */
    public static function today(): array
    {
        list($year, $month, $day) = explode(separator: '-', string: date(format: 'Y-m-d'));
        $year  = intval($year);
        $month = intval($month);
        $day   = intval($day);

        return [
            mktime(hour: 0, minute: 0, second: 0, month: $month, day: $day, year: $year),
            mktime(hour: 23, minute: 59, second: 59, month: $month, day: $day, year: $year)
        ];
    }

    /**
     * 返回昨日开始和结束的时间戳
     * @return array
     */
    public static function yesterday(): array
    {
        $yesterday = date(format: 'd') - 1;
        $yesterday = intval($yesterday);

        return [
            mktime(hour: 0, minute: 0, second: 0, month: date(format: 'm'), day: $yesterday, year: date(format: 'Y')),
            mktime(hour: 23, minute: 59, second: 59, month: date(format: 'm'), day: $yesterday, year: date(format: 'Y'))
        ];
    }

    /**
     * 返回本周开始和结束的时间戳
     * @return array
     */
    public static function week(): array
    {
        list($year, $month, $day, $week) = explode(separator: '-', string: date(format: 'Y-m-d-w'));
        $year  = intval($year);
        $month = intval($month);
        $day   = intval($day);
        $week  = intval($week);

        if ($week == 0)
            $week = 7; //修正周日的问题
        return [
            mktime(hour: 0, minute: 0, second: 0, month: $month, day: $day - $week + 1, year: $year),
            mktime(hour: 23, minute: 59, second: 59, month: $month, day: $day - $week + 7, year: $year)
        ];
    }

    /**
     * 返回上周开始和结束的时间戳
     * @return array
     */
    public static function lastWeek(): array
    {
        $timestamp = time();
        return [
            strtotime(date(format: 'Y-m-d', timestamp: strtotime(datetime: "last week Monday", baseTimestamp: $timestamp))),
            strtotime(date(format: 'Y-m-d', timestamp: strtotime(datetime: "last week Sunday", baseTimestamp: $timestamp))) + 24 * 3600 - 1
        ];
    }

    /**
     * 返回本月开始和结束的时间戳
     * @return array
     */
    public static function month(): array
    {
        list($year, $month, $time) = explode(separator: '-', string: date(format: 'Y-m-t'));
        $year  = intval($year);
        $month = intval($month);
        $time  = intval($time);

        return [
            mktime(hour: 0, minute: 0, second: 0, month: $month, day: 1, year: $year),
            mktime(hour: 23, minute: 59, second: 59, month: $month, day: $time, year: $year)
        ];
    }

    /**
     * 返回上个月开始和结束的时间戳
     * @return array
     */
    public static function lastMonth(): array
    {
        $year  = date(format: 'Y');
        $month = date(format: 'm');
        $year  = intval($year);
        $month = intval($month);

        $begin = mktime(hour: 0, minute: 0, second: 0, month: $month - 1, day: 1, year: $year);
        $end   = mktime(hour: 23, minute: 59, second: 59, month: $month - 1, day: date(format: 't', timestamp: $begin), year: $year);

        return [$begin, $end];
    }

    /**
     * 返回今年开始和结束的时间戳
     * @return array
     */
    public static function year(): array
    {
        $year = date(format: 'Y');
        $year = intval($year);

        return [
            mktime(hour: 0, minute: 0, second: 0, month: 1, day: 1, year: $year),
            mktime(hour: 23, minute: 59, second: 59, month: 12, day: 31, year: $year)
        ];
    }

    /**
     * 返回去年开始和结束的时间戳
     * @return array
     */
    public static function lastYear(): array
    {
        $year = date(format: 'Y') - 1;
        $year = intval($year);

        return [
            mktime(hour: 0, minute: 0, second: 0, month: 1, day: 1, year: $year),
            mktime(hour: 23, minute: 59, second: 59, month: 12, day: 31, year: $year)
        ];
    }

    /**
     * 获取带毫秒的时间格式
     * @param string $format 格式
     * @return string
     */
    public static function getMilliTime(string $format = 'Y-m-d H:i:s'): string
    {
        $milli_timestamp = sprintf(format: "%.3f", values: microtime(as_float: true)); // 带毫秒的时间戳

        $timestamp = intval($milli_timestamp); // 时间戳

        $milliseconds = round(num: ($milli_timestamp - $timestamp) * 1000); // 毫秒

        return date($format, $timestamp) . '.' . $milliseconds;
    }

    /**
     * 获取每月的开始时间
     * @param DateTime $dateTime 日期
     * @return DateTime
     * @throws Exception
     */
    public static function getMonthStartTime(DateTime $dateTime): DateTime
    {
        return new (new DateTime($dateTime->format(format: 'Y-m-01')));
    }

    /**
     * 获取每月的结束时间
     * @param DateTime $dateTime 日期
     * @return DateTime
     * @throws Exception
     */
    public static function getMonthEndTime(DateTime $dateTime): DateTime
    {
        return self::getMonthStartTime($dateTime)->add(new DateInterval(duration: 'P1M'))->sub(new DateInterval(duration: 'PT1S'));
    }

    /**
     * 获取下一个月的开始时间
     * @param DateTime $dateTime 日期
     * @return DateTime
     * @throws Exception
     */
    public static function getNextMonthStartTime(DateTime $dateTime): DateTime
    {
        return self::getMonthStartTime($dateTime)->add(new DateInterval('P1M'));
    }

    /**
     * 检查是否为日期
     * @param string $date 待检查日期
     * @return bool
     */
    public static function isDate(string $date): bool
    {
        //匹配日期格式
        if (preg_match(pattern: "/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})(.*)/", subject: $date, matches: $parts)) {
            //检测是否为日期
            if (checkdate(intval($parts[2]), intval($parts[3]), intval($parts[1])))
                return true;
            else
                return false;
        } else {
            return false;
        }
    }

    /**
     * 计算两个日期之前的差额
     * @param string $dateOne 日期1
     * @param string $dateTwo 日期2
     * @return float
     */
    public static function diffDay(string $dateOne, string $dateTwo): float
    {
        $date1 = strtotime($dateOne);
        $date2 = strtotime($dateTwo);
        return floor(abs(($date1 - $date2) / 86400));
    }

    /**
     * 日期循环
     * @param string $start 开时日期
     * @param string $end 结束日期
     * @param mixed $callback 回调函数
     * @param bool $isHasEnd 是否包含最后一天,默认为false
     * @return void
     * @throws Exception
     */
    public static function eachDate(string $start, string $end, mixed $callback, bool $isHasEnd = false): void
    {
        $calDate = new DateTime($start);
        $endDate = new DateTime($end);
        do {
            if (is_callable($callback)) {
                $result = $callback($calDate);
                if ($result === false) {
                    break;
                }
            }
            $calDate = $calDate->add(new DateInterval(duration: 'P1D'));
        } while ($isHasEnd ? ($calDate <= $endDate) : ($calDate < $endDate));
    }

    /**
     * 判断开始时间在结束时间之前
     * @param string $start 开始时间
     * @param string $end 结束时间
     * @return bool
     * @throws Exception
     */
    public static function before(string $start, string $end): bool
    {
        $render    = false;
        $startDate = new DateTime($start);
        $endDate   = new DateTime($end);
        if ($startDate->getTimestamp() < $endDate->getTimestamp()) {
            $render = true;
        }
        return $render;
    }

    /**
     * 判断开始时间在结束之前或开始时间等于结束时间
     * @param string $start 开始时间
     * @param string $end 结束时间
     * @return bool
     * @throws Exception
     */
    public static function beforeAndEq(string $start, string $end): bool
    {
        $render    = false;
        $startDate = new DateTime($start);
        $endDate   = new DateTime($end);
        if ($startDate->getTimestamp() <= $endDate->getTimestamp()) {
            $render = true;
        }
        return $render;
    }

    /**
     * 判断开始时间在结束时间之后
     * @param string $start 开始时间
     * @param string $end 结束时间
     * @return bool
     * @throws Exception
     */
    public static function after(string $start, string $end): bool
    {
        $render    = false;
        $startDate = new DateTime($start);
        $endDate   = new DateTime($end);
        if ($startDate->getTimestamp() > $endDate->getTimestamp()) {
            $render = true;
        }
        return $render;
    }

    /**
     * 判断开始时间在结束之后或开始时间等于结束时间
     * @param string $start 开始时间
     * @param string $end 结束时间
     * @return bool
     * @throws Exception
     */
    public static function afterAndEq(string $start, string $end): bool
    {
        $render    = false;
        $startDate = new DateTime($start);
        $endDate   = new DateTime($end);
        if ($startDate->getTimestamp() >= $endDate->getTimestamp()) {
            $render = true;
        }
        return $render;
    }


    /**
     * 获取时间较大的
     * @param mixed $start 开始时间
     * @param mixed $end 结束时间
     * @return mixed
     * @throws Exception
     */
    public static function getMax(mixed $start, mixed $end): mixed
    {
        $render = null;
        if (!is_null($start) && is_null($end)) {
            $render = $start;
        } elseif (is_null($start) && !is_null($end)) {
            $render =$end;
        } elseif (!is_null($start) && !is_null($end) && static::before($start,$end)) {
            $render = $end;
        }

        return $render;
    }

    /**
     * 获取时间较小的
     * @param mixed $start 开始时间
     * @param mixed $end 结束时间
     * @return mixed
     * @throws Exception
     */
    public static function getMin(mixed $start, mixed $end): mixed
    {
        $render = null;
        if (!is_null($start) && is_null($end)) {
            $render = $start;
        } elseif (is_null($start) && !is_null($end)) {
            $render = $end;
        } elseif (!is_null($start) && !is_null($end) && static::after($start,$end)) {
            $render = $end;
        }
        return $render;
    }

    /**
     * 判断开始时间是否等于结束时间
     * @param string $start 开始时间
     * @param string $end 结束时间
     * @return bool
     * @throws Exception
     */
    public static function eq(string $start, string $end): bool
    {

        $render    = false;
        $startDate = new DateTime($start);
        $endDate   = new DateTime($end);
        if ($startDate->getTimestamp() == $endDate->getTimestamp()) {
            $render = true;
        }
        return $render;
    }

    /**
     * 获取日期的时间戳
     * @param mixed $date 日期
     * @return float|int|string
     * @throws Exception
     */
    public static function timestamp(mixed $date): float|int|string
    {
        $time = 0;
        if ($date instanceof DateTime) {
            $time = $date->getTimestamp();
        } elseif (is_string($date)) {
            $time = (new DateTime($date))->getTimestamp();
        } elseif (is_numeric($date)) {
            $time = $date;
        }

        return $time;
    }

    /**
     * 格式化日期
     * @param string $format 格式
     * @param mixed $date 日期
     * @return string
     * @throws Exception
     */
    public static function format(mixed $format, mixed $date = null): string
    {
        $time = static::timestamp($date);
        if ($time ==0 ) {
            $time = time();
        }

        if (!is_numeric($time)) {
            throw new Exception(message: '时间格式错误:'.$date);
        }

        return date($format,$time);
    }
}