<?php
namespace App\Services\Payment\Contracts;
use App\Models\Payment;
interface PaymentGatewayInterface
{
    public function code(): string;

    /**
     * Reject an unavailable provider before creating a pending payment attempt.
     */
    public function assertConfigured(): void;

    /**
     * @return array{type: string, instructions?: string, checkout_url?: string}
     */
    public function initiate(Payment $payment): array;

    public function verifyWebhook(array $payload, array $headers): bool;

    public function queryStatus(string $providerReference): array;
}
