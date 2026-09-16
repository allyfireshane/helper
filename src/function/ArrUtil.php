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

use Exception;

class ArrUtil
{
    /**
     * 将二维数组按某个键提取出来组成新的索引数组
     * @param array $array 二维数组内容
     * @param string $key 提取关键字
     * @return array
     */
    public static function extract(array $array, string $key = "id"): array
    {
        $arr = [];
        foreach ($array as $value) {
            if ($value[$key]) {
                $arr[] = $value[$key];
            }
        }

        return $arr;
    }

    /**
     * 将多维数组按某个键提取出来组成新的索引数组（合并
     * @param array $arr 多位数组内容
     * @param string $key 关键字
     * @param int $level 提前层级
     * @return array
     */
    public static function mergeExtract(array $arr, string $key = 'id', int $level = 1): array
    {
        $target = [];
        $temp = [];
        foreach ($arr as $val) {
            if (!empty($target)) {
                if (in_array(needle: $val[$key], haystack: array_keys(array: $target))) {
                    if (!empty($temp[$val[$key]])) {
                        $target[$val[$key]] = null;
                        if ($level > 1) {
                            $target[$val[$key]][] = $temp[$val[$key]];
                        } else {
                            $target[$val[$key]] = $temp[$val[$key]];
                        }

                        $temp[$val[$key]] = null;
                    }
                } else {
                    $temp[$val[$key]] = $val;
                }

            } else {
                $temp[$val[$key]] = $val;
            }
            if ($level > 1) {
                $target[$val[$key]][] = $val;
            } else {
                $target[$val[$key]] = $val;
            }
        }

        return $target;
    }

    /**
     * 分割变量,转为数组
     * @param mixed $str 原始内容
     * @param string $separator 分隔符
     * @param bool $trimFlag 是否清除空格  true - 是， false - 否
     * @return mixed
     * @throws Exception
     */
    public static function explode(mixed $str, string $separator = ',', bool $trimFlag = true): mixed
    {
        if (is_string($str)) {
            $render = explode($separator, $str);
        } elseif (is_numeric($str)) {
            $render = [$str];
        } elseif (is_array($str)) {
            $render = $str;
        } else {
            throw new Exception(message: "不支持当前输入格式");
        }

        if (!is_array($render)) {
            $render = [];
        }

        if ($trimFlag) {
            $_tmp = [];
            foreach ($render as $row) {
                if(is_string($row)){
                    $row = trim($row);
                }

                if ($row) {
                    $_tmp[] = $row;
                }
            }
            $render = $_tmp;
        }

        return $render;
    }

    /**
     * 转换为json字符串
     * @param mixed $val 转换前内容
     * @return string
     */
    public static function toJson(mixed $val): string
    {
        return json_encode($val, flags: JSON_UNESCAPED_UNICODE) ?: "";
    }

    /**
     * json字符串转换为数组
     * @param mixed $val json字符串
     * @return array
     */
    public static function toArray(mixed $val): array
    {
        $val = is_string($val) ? $val : '';
        if (is_array($val)) {
            return $val;
        }
        if(is_object($val)){
            return (array)$val;
        }
        return json_decode($val,associative: true) ?: [];
    }

    /**
     * 从特定内容中提取关键字段内容
     * @param mixed $detail 指定内容
     * @param string $path 提取路径
     * @param mixed|null $default 默认值
     * @param string $separator 分隔符
     * @return mixed|null
     */
    public static function getVarPath(mixed &$detail, string $path, mixed $default = null, string $separator = '.'): mixed
    {
        $render = self::_getVarPath($detail, $path, $default, $separator);

        if (is_null($render)) {
            $render = $default;
        }

        return $render;
    }

    /**
     * 根据关键字设置特定内容相应的值
     * @param array $detail 指定内容
     * @param string $path 指定路径
     * @param string|null $default 默认值
     * @return array
     */
    public static function setVarPath(array &$detail, string $path, string $default = null): array
    {
        self::_setVarPath(detail: $detail, path: $path, default: $default);
        return $detail;
    }

    /**
     * 根据关键字合并设置特定内容相应的值
     * @param array $detail 指定内容
     * @param array $config 配置信息
     * @return array
     */
    public function batchSetVarPath(array &$detail, array $config): array
    {
        foreach ($config as $path => $value) {
            $detail = self::setVarPath($detail, $path, $value);
        }
        return $detail;
    }

    /**
     * 从特定内容中提取关键字段内容
     * @param mixed $detail 指定内容
     * @param string $path 提取路径
     * @param string $separator 分隔符
     * @param bool $first 是否第一层
     * @return mixed|void|null
     */
    protected static function _getVarPath(mixed &$detail, string $path, string $separator = '.', bool $first = true)
    {
        $paths = explode($separator, $path);

        if (count($paths) == 0) {
            if ($first) {
                return;
            }
        }
        $top_path = array_shift(array: $paths);
        $render   = null;

        if (is_object($detail)) {
            if (isset($detail->$top_path)) {
                if (count($paths) > 0) {
                    $path    = implode($separator, $paths);
                    $_detail = $detail->$top_path;
                    $render  = self::_getVarPath(detail: $_detail, path: $path, separator: $separator, first: false);
                } else {
                    $render = $detail->$top_path;
                }
            }
        } else {
            if (isset($detail[$top_path])) {
                if (count($paths) > 0) {
                    if (is_array($detail[$top_path])||is_object($detail[$top_path])) {
                        $path   = implode($separator, $paths);
                        $render = self::_getVarPath(detail:$detail[$top_path], path: $path, separator: $separator, first: false);
                    }
                } else {
                    $render = $detail[$top_path];
                }
            }
        }
        return $render;
    }

    /**
     * 根据关键字设置特定内容相应的值
     * @param array $detail 指定内容
     * @param string $path 指定路径
     * @param string|null $default 默认值
     * @return void
     */
    protected static function _setVarPath(array &$detail, string $path, string $default = null): void
    {
        $paths = explode(separator: '.', string: $path);
        if (count($paths) == 0) {
            return;
        }
        $top_path = array_shift(array: $paths);
        $subPath  = implode(separator: '.',array: $paths);

        if ($top_path == '*') {
            foreach ($detail as &$val) {
                if ($subPath) {
                    self::_setVarPath(detail: $val, path: $subPath, default: $default);
                } else {
                    $val = $default;
                }
            }
        } else {
            if ($subPath) {
                if (!isset($detail[$top_path])) {
                    $detail[$top_path]=[];
                }
                self::_setVarPath(detail: $detail[$top_path], path: $subPath, default: $default);
            } else {
                $detail[$top_path] = $default;
            }
        }
    }
}