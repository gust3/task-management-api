<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class TaskNotFoundException extends Exception
{
    public function __construct(int $id, int $code = 404, ?Throwable $previous = null)
    {
        $message = "Задача с ID {$id} не найдена";
        parent::__construct($message, $code, $previous);
    }
}