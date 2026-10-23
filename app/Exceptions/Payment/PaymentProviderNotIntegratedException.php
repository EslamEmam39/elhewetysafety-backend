<?php
namespace App\Exceptions\Payment;
use App\Exceptions\BusinessException;
class PaymentProviderNotIntegratedException extends BusinessException
{
    public function __construct(string $code)
    {
        parent::__construct("Payment provider [$code] is not integrated yet", 503);
    }
}
