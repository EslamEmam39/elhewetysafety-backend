<?php

namespace App\Exceptions\Payment;

use App\Exceptions\BusinessException;

class CashPaymentCannotBeConfirmedException extends BusinessException
{
    public function __construct()
    {
        parent::__construct('Only pending cash payments for pending orders can be confirmed.', 409);
    }
}
