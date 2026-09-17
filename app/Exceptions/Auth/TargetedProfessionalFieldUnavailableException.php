<?php

namespace App\Exceptions\Auth;

use App\Exceptions\BusinessException;

class TargetedProfessionalFieldUnavailableException extends BusinessException
{
    public function __construct()
    {
        parent::__construct(
            'The selected targeted professional field is unavailable',
            422,
            ['targeted_professional_field_uuid' => ['The selected targeted professional field is unavailable.']]
        );
    }
}