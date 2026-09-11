<?php

namespace App\Http\Requests\EmailVerification;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Support\Str;

class VerifyEmailOtpRequest extends ApiFormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => Str::lower(trim((string) $this->input('email'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
        ];
    }
}
