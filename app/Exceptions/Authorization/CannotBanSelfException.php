<?php

namespace App\Exceptions\Authorization;

use App\Exceptions\BusinessException;

class CannotBanSelfException extends BusinessException
{
    public function __construct()
    {
        parent::__construct(
            message: 'Administrators cannot ban their own account',
            statusCode: 409
        );
    }
}