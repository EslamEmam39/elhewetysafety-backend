<?php
namespace App\Services\Payment;
use App\Exceptions\Payment\UnsupportedPaymentGatewayException;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Gateways\FawryService;
use App\Services\Payment\Gateways\PaymobService;
class PaymentGatewayFactory
{
    public function __construct(private readonly PaymobService $paymob, private readonly FawryService $fawry) {}
    public function make(string $code): PaymentGatewayInterface
    {
        return match (strtolower($code)) {
            'paymob' => $this->paymob,
            'fawry' => $this->fawry,
            default => throw new UnsupportedPaymentGatewayException($code),
        };
    }
}
