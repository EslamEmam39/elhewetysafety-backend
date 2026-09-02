<?php

namespace App\Exceptions\Authorization;

use App\Exceptions\BusinessException;

class ProtectedSystemRoleException extends BusinessException
{
    public function __construct(string $operation)
    {
        parent::__construct(
            message: "System roles cannot be {$operation}",
            statusCode: 409
        );
    }
}
