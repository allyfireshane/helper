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
namespace allyfireshane\helper\exception;

class RetryException extends BaseException
{
    private int $runNum = 0;
    private int $nextTime = 0;

    public function getRunNum(): int
    {
        return $this->runNum;
    }

    public function setRunNum(int $runNum): void
    {
        $this->runNum = $runNum;
    }

    public function getNextTime(): int
    {
        return $this->nextTime;
    }

    public function setNextTime(int $nextTime): void
    {
        $this->nextTime = $nextTime;
    }
}