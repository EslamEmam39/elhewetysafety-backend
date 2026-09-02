<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

abstract class BusinessException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $statusCode = 400,
        public readonly array $errors = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
