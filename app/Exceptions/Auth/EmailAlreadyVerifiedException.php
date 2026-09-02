<?php

namespace App\Exceptions\Auth;

use App\Exceptions\BusinessException;

class EmailAlreadyVerifiedException extends BusinessException
{
    public function __construct()
    {
        parent::__construct(
            message: 'Email address is already verified',
            statusCode: 409
        );
    }
}
