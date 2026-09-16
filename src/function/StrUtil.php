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
use RangeException;

class StrUtil
{
    /**
     * 获取指定长度的随机字母数字组合的字符串
     * @param int $length 生成长度
     * @param int|null $type 类型 默认 - 大小写字母和数字 0 - 全字母（包含大小写） 1 - 全数字 2 - 全大写字母 3 - 全小写字母 4 - 全中文
     * @param string $addChars 额外内容
     * @return string
     */
    public static function random(int $length = 6, int $type = null, string $addChars = ''): string
    {
        $str = '';
        $chars = match ($type) {
            0 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars,
            1 => str_repeat(string: '0123456789', times: 3),
            2 => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars,
            3 => 'abcdefghijklmnopqrstuvwxyz' . $addChars,
            4 => "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书" . $addChars,
            default => 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars,
        };

        if ($length > 10) {
            $chars = $type == 1 ? str_repeat(string: $chars, times:  $length) : str_repeat(string: $chars, times: 5);
        }

        if ($type != 4) {
            $chars = str_shuffle($chars);
            $str = substr(string: $chars, offset: 0, length: $length);
        } else {
            for ($i = 0; $i < $length; $i++) {
                $str .= mb_substr($chars, intval(floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1))), 1);
            }
        }
        return $str;
    }

    /**
     * 给url追加参数
     * @param string $url url路径
     * @param array $params 参数集
     * @return string
     */
    public static function addQueryParams(string $url, array $params): string
    {
        // 解析URL的各个部分
        $urlParts = parse_url($url);

        // 解析原始查询参数
        $existingParams = [];
        if (isset($urlParts['query'])) {
            parse_str($urlParts['query'], result: $existingParams);
        }

        // 合并参数（新参数覆盖同名旧参数）
        $mergedParams = array_merge($existingParams, $params);

        // 重新构建查询字符串，自动进行URL编码
        $newQuery = http_build_query($mergedParams);

        // 重建完整的URL
        $newUrl = '';
        if (isset($urlParts['scheme'])) {
            $newUrl .= $urlParts['scheme'] . '://';
        }
        if (isset($urlParts['host'])) {
            $newUrl .= $urlParts['host'];
        }
        if (isset($urlParts['port'])) {
            $newUrl .= ':' . $urlParts['port'];
        }
        if (isset($urlParts['path'])) {
            $newUrl .= $urlParts['path'];
        }
        if ($newQuery) {
            $newUrl .= '?' . $newQuery;
        }
        if (isset($urlParts['fragment'])) {
            $newUrl .= '#' . $urlParts['fragment'];
        }

        return $newUrl;
    }

    /**
     * 生成UUID
     * @return string
     */
    public static function generateUUID(): string {
        if (function_exists(function: 'com_create_guid')) {
            return strtolower(trim(string: com_create_guid(), characters: '{}'));
        } else {
            try {
                $data = random_bytes(length: 16);
                $data[6] = chr(codepoint: ord($data[6]) & 0x0f | 0x40); // 设置版本为4
                $data[8] = chr(codepoint: ord($data[8]) & 0x3f | 0x80); // 设置变体为10
            } catch (Exception $e) {
                throw new RangeException($e->getMessage());
            }


            return vsprintf(format: '%s%s-%s-%s-%s-%s%s%s', values: str_split(bin2hex($data), length: 4));
        }
    }
}