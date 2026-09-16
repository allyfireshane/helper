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

class ExportCsvHelper
{
    /**
     * @param array $headers
     * @param string $tmpFilePath
     * @param int $num
     * @param $handle
     * @throws Exception
     */
    public function __construct(
        private array $headers = [],
        private string $tmpFilePath,
        private int $num = 0,
        private $handle
    )
    {
        $this->tmpFilePath= FileUtil::tmpFile();
        $this->handle = fopen(filename: $this->tmpFilePath, mode: 'wb');
        $header  = array_values($headers);
        fputcsv($this->handle, $header);
    }

    /**
     * 插入内容
     * @param int $row 插入行数
     * @return void
     */
    public function append(int $row): void
    {
        $data=[];
        foreach($this->headers as $key=>$header){
            $data[]= ArrUtil::getVarPath(detail: $row, path: $key) ?: '';
        }
        fputcsv($this->handle, $data);
        $this->num++;
    }

    /**
     * 批量插入行
     * @param array $rowList 插入行列表
     * @return void
     */
    public function appendList(array $rowList): void
    {
        foreach($rowList as $row){
            $this->append($row);
        }
    }

    /**
     * 获取当前行数
     * @return int
     */
    public function getRowNum(): int
    {
        return $this->num;
    }

    /**
     * 获取生成的临时文件路径
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->tmpFilePath;
    }

    /**
     * 关闭文件流操作
     * @return void
     */
    public function closeFile(): void
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
            $this->handle=null;
        }
    }
}