<?php
namespace App\Exceptions\Payment;
use App\Exceptions\BusinessException;
class PaymentGatewayUnavailableException extends BusinessException
{
    public function __construct() { parent::__construct('The selected payment gateway is unavailable', 422, ['gateway_code'=>['The selected gateway is unavailable.']]); }
}
