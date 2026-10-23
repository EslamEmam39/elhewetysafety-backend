<?php

namespace App\Services\Payment\Gateways;

use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;

class CashService implements PaymentGatewayInterface
{
    public function code(): string
    {
        return 'cash';
    }

    public function assertConfigured(): void
    {
        // Cash confirmation is an internal administrative workflow.
    }

    public function initiate(Payment $payment): array
    {
        return [
            'type' => 'cash_confirmation',
            'instructions' => config('services.cash.instructions')
                ?: 'Your cash payment is pending administrative confirmation.',
        ];
    }

    public function verifyWebhook(array $payload, array $headers): bool
    {
        return false;
    }

    public function queryStatus(string $providerReference): array
    {
        return ['status' => 'pending'];
    }
}
