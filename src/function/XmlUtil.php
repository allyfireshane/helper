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

class XmlUtil
{

    /**
     * 读取xml内容并解析成数组
     * @param mixed $content xml内容
     * @return array[]
     */
    public static function parseToArray(mixed $content): array
    {
        $xml = simplexml_load_string($content);
        return static::xmlToArray($xml);
    }

    /**
     * xml转换成数组
     * @param mixed $xml xml内容
     * @return array[]
     */
    public static function xmlToArray(mixed $xml): array
    {
        $parser = function ($xml, array $collection = []) use (&$parser) {
            $nodes = $xml->children();
            $attributes = $xml->attributes();

            if (0 !== count($attributes)) {
                foreach ($attributes as $attrName => $attrValue) {
                    $collection['attributes'][$attrName] = strval($attrValue);
                }
            }

            if (0 === $nodes->count()) {
                $collection['value'] = strval($xml);
                return $collection;
            }

            foreach ($nodes as $nodeName => $nodeValue) {
                if (count($nodeValue->xpath('../' . $nodeName)) < 2) {
                    $collection[$nodeName] = $parser($nodeValue);
                    continue;
                }

                $collection[$nodeName][] = $parser($nodeValue);
            }

            return $collection;
        };

        return [
            $xml->getName() => $parser($xml)
        ];
    }
}