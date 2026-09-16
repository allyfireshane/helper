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

class BaseException extends \Exception
{
    private mixed $errorData;

    public function setErrorData(mixed $data): BaseException
    {
        $this->errorData = $data;
    }

    public function getErrorData(): mixed
    {
        return $this->errorData;
    }
}