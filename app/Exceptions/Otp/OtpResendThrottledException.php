<?php

namespace App\Exceptions\Otp;

use App\Exceptions\BusinessException;

class OtpResendThrottledException extends BusinessException
{
    public function __construct(int $retryAfterSeconds)
    {
        parent::__construct(
            message: "Please wait {$retryAfterSeconds} seconds before requesting another code",
            statusCode: 429
        );
    }
}
