<?php

namespace App\Exceptions\Payment;

use App\Exceptions\BusinessException;

class PaymentGatewayConfigurationException extends BusinessException
{
    public function __construct(string $gateway)
    {
        parent::__construct("Payment gateway [$gateway] is not configured.", 503);
    }
}
