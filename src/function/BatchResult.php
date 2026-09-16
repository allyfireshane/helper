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

class BatchResult
{
    public function __construct(
        private array $successList = [],
        private array $errorList = [],
    )
    {
    }

    public function getSuccessList(): array
    {
        return $this->successList;
    }

    public function setSuccessList(array $successList): void
    {
        $this->successList = $successList;
    }

    public function getErrorList(): array
    {
        return $this->errorList;
    }

    public function setErrorList(array $errorList): void
    {
        $this->errorList = $errorList;
    }

    public function addSuccess(mixed $success): void
    {
        $this->successList[] = $success;
    }

    public function addError(mixed $error): void
    {
        $this->errorList[] = $error;
    }
}