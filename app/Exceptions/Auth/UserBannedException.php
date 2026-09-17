<?php

namespace App\Exceptions\Auth;

use App\Exceptions\BusinessException;

class UserBannedException extends BusinessException
{
    public function __construct(?string $reason = null)
    {
        parent::__construct(
            message: 'Your account has been banned',
            statusCode: 403,
            errors: $reason ? ['ban_reason' => [$reason]] : []
        );
    }
}