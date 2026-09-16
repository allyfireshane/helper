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

class NumberUtil
{
    /**
     * 判断浮点数是否相等
     * @param mixed $one 浮点数1
     * @param mixed $two 浮点数2
     * @param int $floatNumber 浮点位数
     * @return bool
     */
    public static function eqFloat(mixed $one, mixed $two, int $floatNumber = 4): bool
    {
        return self::formatFloatString($one, $floatNumber) === self::formatFloatString($two, $floatNumber);
    }

    /**
     * 格式化浮点数
     * @param mixed $num 需要格式化的浮点数
     * @param int $floatNumber 浮点位数
     * @return float
     */
    public static function formatFloat(mixed $num, int $floatNumber = 4): float
    {
        return floatval(self::formatFloatString($num, $floatNumber));
    }

    /**
     * 格式化浮点数
     * @param mixed $num 需要格式化的浮点数
     * @param int $floatNumber 浮点位数
     * @return string
     */
    public static function formatFloatString(mixed $num, int $floatNumber = 4): string
    {
        $format = '%.' . $floatNumber . 'f';
        return sprintf($format, $num);
    }
}