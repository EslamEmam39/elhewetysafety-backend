<?php

namespace App\Exceptions\Auth;

use App\Exceptions\BusinessException;

class EmailNotVerifiedException extends BusinessException
{
    public function __construct()
    {
        parent::__construct(
            message: 'Please verify your email address before signing in',
            statusCode: 403,
            errors: ['email' => ['Please verify your email address before signing in.']]
        );
    }
}
