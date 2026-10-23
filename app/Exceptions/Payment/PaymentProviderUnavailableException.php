<?php

namespace App\Exceptions\Payment;

use App\Exceptions\BusinessException;

class PaymentProviderUnavailableException extends BusinessException
{
    public function __construct(string $gateway)
    {
        parent::__construct("Payment provider [$gateway] is temporarily unavailable. Please try again.", 503);
    }
}
