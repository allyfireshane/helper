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
use ResourceBundle;

class FileUtil
{
    /**
     * 创建目录
     * @param string $path 文件夹路径
     * @return bool
     */
    public static function createDir(string $path): bool
    {
        $path = str_replace(search: '', replace: '/', subject: $path);
        $dir = '';
        $pathArr = explode(separator: '/', string: $path);
        $result = true;
        foreach ($pathArr as $value) {
            $dir .= $value . '/';
            if (!file_exists(filename: $dir)) {
                $result = mkdir(directory: $dir);
            }
        }

        return $result;
    }

    /**
     * 移动文件夹
     * @param string $oldDir 需要移动的文件夹
     * @param string $aimDir 目前文件夹
     * @param bool $overwrite 是否覆盖
     * @return bool
     */
    public static function moveDir(string $oldDir, string $aimDir, bool $overwrite = false): bool
    {
        $aimDir = str_replace(search: '', replace: '/', subject: $aimDir);
        $aimDir = str_ends_with($aimDir, needle: '/') ? $aimDir : $aimDir . '/';
        $oldDir = str_replace(search: '', replace:  '/', subject: $oldDir);
        $oldDir = str_ends_with($oldDir, needle: '/') ? $oldDir : $oldDir . '/';
        if (!is_dir(filename: $oldDir)) {
            return false;

        }

        if (!file_exists(filename: $aimDir)) {
            self::createDir(path: $aimDir);
        }

        @$handle = opendir(directory: $oldDir);
        if (!$handle) {
            return false;
        }

        while (false !== ($file = readdir(dir_handle: $handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            if (!is_dir(filename: $oldDir . $file)) {
                self::moveFile(filePath: $oldDir . $file, aimPath: $aimDir . $file, overwrite: $overwrite);
            } else {
                self::moveDir(oldDir: $oldDir . $file, aimDir: $aimDir . $file, overwrite: $overwrite);
            }
        }

        closedir(dir_handle: $handle);
        return rmdir(directory: $oldDir);
    }

    /**
     * 新建文件
     * @param string $path 文件保存路径
     * @param bool $overwrite 是否覆盖
     * @return bool
     */
    public static function createFile(string $path, bool $overwrite = false): bool
    {
        if (file_exists(filename: $path)) {
            if ($overwrite) {
                self::unlinkFile(path: $path);
            } else {
                return false;
            }
        }

        $dir = dirname(path: $path);
        self::createDir(path: $dir);
        touch(filename: $path);
        return true;
    }

    /**
     * 移动文件
     * @param string $filePath 原文件所在位置
     * @param string $aimPath 目前文件位置
     * @param bool $overwrite 是否覆盖
     * @return bool
     */
    public static function moveFile(string $filePath, string $aimPath, bool $overwrite = false): bool
    {
        if (!file_exists(filename: $filePath)) {
            return false;
        }

        if (file_exists(filename: $aimPath)) {
            if ($overwrite) {
                self::unlinkFile(path: $aimPath);
            } else {
                return false;
            }
        }

        $aimDir = dirname(path: $aimPath);
        self::createDir(path: $aimDir);
        rename(from: $filePath, to: $aimPath);
        return true;
    }

    /**
     * 删除文件夹
     * @param string $dir 需要删除的文件夹路径
     * @return bool
     */
    public static function unlinkDir(string $dir): bool
    {
        $dir = str_replace(search: '', replace: '/', subject: $dir);
        $dir = str_ends_with($dir, needle: '/') ? $dir : $dir . '/';
        if (!is_dir($dir)) {
            return false;
        }

        $handle = opendir($dir);
        while (false !== ($file = readdir($handle))) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if (!is_dir(filename: $dir . $file)) {
                self::unlinkFile(path: $dir . $file);
            } else {
                self::unlinkDir(dir: $dir . $file);
            }
        }

        closedir($handle);
        return rmdir($dir);
    }

    /**
     * 删除文件
     * @param string $path 文件所在完整路径
     * @return bool
     */
    public static function unlinkFile(string $path): bool
    {
        if (file_exists($path)) {
            unlink($path);
            return true;
        }

        return false;
    }

    /**
     * 复制文件夹
     * @param string $oldDir 原文件夹路径
     * @param string $aimDir 目标文件夹路径
     * @param bool $overwrite 是否覆盖
     * @return bool
     */
    public static function copyDir(string $oldDir, string $aimDir, bool $overwrite = false): bool
    {
        $aimDir = str_replace(search: '', replace:  '/', subject: $aimDir);
        $aimDir = str_ends_with($aimDir, needle: '/') ? $aimDir : $aimDir . '/';
        $oldDir = str_replace(search: '', replace: '/', subject: $oldDir);
        $oldDir = str_ends_with($oldDir, needle: '/') ? $oldDir : $oldDir . '/';
        if (!is_dir($oldDir)) {
            return false;
        }

        if (!file_exists($aimDir)) {
            self::createDir($aimDir);
        }

        $handle = opendir($oldDir);
        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            if (!is_dir(filename: $oldDir . $file)) {
                self::copyFile(filePath: $oldDir . $file, aimPath: $aimDir . $file, overwrite: $overwrite);
            } else {
                self::copyDir(oldDir: $oldDir . $file, aimDir: $aimDir . $file, overwrite: $overwrite);
            }
        }

        closedir($handle);
        return true;
    }

    /**
     * 复制文件
     * @param string $filePath 原文件完整路径
     * @param string $aimPath 目前文件路径
     * @param bool $overwrite 是否覆盖
     * @return bool
     */
    public static function copyFile(string $filePath, string $aimPath, bool $overwrite = false): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        if (file_exists($aimPath)) {
            if ($overwrite) {
                self::unlinkFile($aimPath);
            } else {
                return false;
            }
        }

        $aimDir = dirname($aimPath);
        self::createDir($aimDir);
        copy($filePath, $aimPath);
        return true;
    }

    /**
     * 判断文件夹是否为空
     * @param string $dir 文件夹完整路径
     * @return bool
     */
    public static function isEmptyDir(string $dir): bool
    {
        if (!is_dir($dir)) {
            return true;
        }

        $res = array_diff(scandir($dir), ['..', '.']);
        return empty($res);
    }

    /**
     * 创建临时文件
     * @param string $path 文件保存路径
     * @param string $prefix 文件名前缀
     * @return string
     * @throws Exception
     */
    public static function tmpFile(string $path = "./runtime/tmpfile/", string $prefix = ''): string
    {
        $times   = 6;

        $dirPath = rtrim(string: $path . $prefix, characters: '/,' . DIRECTORY_SEPARATOR);
        if (!is_dir($dirPath)) {
            self::createDir($dirPath);
        }
        do {

            $filepath = $dirPath . '/' . Str::random(length: 8) . '.tmp';

            $times--;
            if (!file_exists($filepath)) {
                break;
            }
            if ($times <= 0) {
                throw new Exception(message: '创建临时文件失败' . $filepath);
            }
        } while (true);
        self::createFile($filepath);
        register_shutdown_function(function ()use ($filepath)
        {
            self::unlinkFile($filepath);
        });

        return $filepath;
    }

    /**
     * 临时文件处理
     * @param string $content
     * @return bool|ResourceBundle
     */
    public static function tmpFileHandle(string $content = ''): bool|ResourceBundle
    {
        $render = tmpfile();
        if ($content) {
            @fwrite($render, $content);
            fseek(stream: $render, offset: SEEK_SET, whence: 0);
        }
        return $render;
    }
}