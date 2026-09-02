<?php

namespace App\Exceptions\Otp;

use App\Exceptions\BusinessException;

class TooManyOtpAttemptsException extends BusinessException
{
    public function __construct()
    {
        parent::__construct(
            message: 'Too many verification attempts. Request a new code',
            statusCode: 429,
            errors: ['otp' => ['Request a new verification code.']]
        );
    }
}
