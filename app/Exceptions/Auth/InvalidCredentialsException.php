<?php

namespace App\Exceptions\Auth;

use App\Exceptions\BusinessException;

class InvalidCredentialsException extends BusinessException
{
    public function __construct()
    {
        parent::__construct(
            message: 'Invalid credentials',
            statusCode: 422,
            errors: [
                'email' => ['Invalid credentials'],
            ]
        );
    }
}
