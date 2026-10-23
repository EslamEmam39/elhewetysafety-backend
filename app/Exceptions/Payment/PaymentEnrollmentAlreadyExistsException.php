<?php

namespace App\Exceptions\Payment;

use App\Exceptions\BusinessException;

class PaymentEnrollmentAlreadyExistsException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('This purchase already has an enrollment record.', 409);
    }
}
