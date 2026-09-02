<?php

namespace App\Http\Requests\EmailVerification;

use App\Http\Requests\ApiFormRequest;

class VerifyEmailOtpRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'otp' => ['required', 'digits:6'],
        ];
    }
}
