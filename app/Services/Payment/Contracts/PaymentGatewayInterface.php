<?php
namespace App\Services\Payment\Contracts;
use App\Models\Payment;
interface PaymentGatewayInterface
{
    public function code(): string;
    public function initiate(Payment $payment): array;
    public function verifyWebhook(array $payload, array $headers): bool;
    public function queryStatus(string $providerReference): array;
}
