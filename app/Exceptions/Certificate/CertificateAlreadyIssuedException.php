<?php

namespace App\Exceptions\Certificate;

use App\Exceptions\BusinessException;

class CertificateAlreadyIssuedException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('A certificate has already been issued for this student and course.', 409);
    }
}
