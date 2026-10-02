<?php
namespace App\Exceptions\Payment;
use App\Exceptions\BusinessException;
class UnsupportedPaymentGatewayException extends BusinessException
{
    public function __construct(string $code) { parent::__construct("Payment gateway [$code] is not supported", 422); }
}
