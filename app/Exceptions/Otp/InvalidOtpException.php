<?php

namespace App\Exceptions\Otp;

use App\Exceptions\BusinessException;

class InvalidOtpException extends BusinessException
{
    public function __construct()
    {
        parent::__construct(
            message: 'The verification code is invalid',
            statusCode: 422,
            errors: ['otp' => ['The verification code is invalid.']]
        );
    }
}
