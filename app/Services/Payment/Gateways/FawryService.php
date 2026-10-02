<?php
namespace App\Services\Payment\Gateways;
use App\Exceptions\Payment\PaymentProviderNotIntegratedException;
use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
class FawryService implements PaymentGatewayInterface
{
    public function code(): string { return 'fawry'; }
    public function initiate(Payment $payment): array { throw new PaymentProviderNotIntegratedException($this->code()); }
    public function verifyWebhook(array $payload, array $headers): bool { throw new PaymentProviderNotIntegratedException($this->code()); }
    public function queryStatus(string $providerReference): array { throw new PaymentProviderNotIntegratedException($this->code()); }
}
