<?php

namespace App\Exceptions\Otp;

use App\Exceptions\BusinessException;

class ExpiredOtpException extends BusinessException
{
    public function __construct()
    {
        parent::__construct(
            message: 'The verification code has expired',
            statusCode: 422,
            errors: ['otp' => ['The verification code has expired.']]
        );
    }
}
