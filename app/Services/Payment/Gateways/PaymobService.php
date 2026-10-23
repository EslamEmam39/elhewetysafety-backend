<?php

namespace App\Services\Payment\Gateways;

use App\Exceptions\Payment\PaymentGatewayConfigurationException;
use App\Exceptions\Payment\PaymentProviderUnavailableException;
use App\Models\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobService implements PaymentGatewayInterface
{
    private const BASE_URL = 'https://accept.paymob.com';

    public function code(): string
    {
        return 'paymob';
    }

    public function assertConfigured(): void
    {
        $missing = [];

        foreach ([
            'secret_key',
            'public_key',
            'integration_id',
            'hmac_secret',
            'notification_url',
            'redirection_url',
        ] as $key) {
            if (blank(config("services.paymob.{$key}"))) {
                $missing[] = $key;
            }
        }

        if ($missing !== []) {
            Log::warning('Paymob configuration is incomplete.', ['missing_keys' => $missing]);

            throw new PaymentGatewayConfigurationException($this->code());
        }
    }

    public function initiate(Payment $payment): array
    {
        $this->assertConfigured();

        $payment->loadMissing('order.user');
        $user = $payment->order->user;

        $parts = preg_split('/\s+/', trim($user->full_name_en ?: $user->username), 2);
        $firstName = $parts[0] ?: 'NA';
        $lastName = $parts[1] ?? 'NA';

        $amountCents = (int) round(((float) $payment->amount) * 100);

        $response = Http::withToken(config('services.paymob.secret_key'), 'Token')
            ->acceptJson()
            ->timeout(20)
            ->post(self::BASE_URL . '/v1/intention/', [
                'amount' => $amountCents,
                'currency' => $payment->currency,
                'payment_methods' => [(int) config('services.paymob.integration_id')],
                'items' => [
                    [
                        'name' => 'Order ' . $payment->order->code,
                        'amount' => $amountCents,
                        'description' => 'Course purchase',
                        'quantity' => 1,
                    ]
                ],
                'billing_data' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $user->email,
                    'phone_number' => $user->phone ?: 'NA',
                ],
                'special_reference' => $payment->code,
                'notification_url' => config('services.paymob.notification_url'),
                'redirection_url' => config('services.paymob.redirection_url'),
            ]);

        if (!$response->successful() || blank($response->json('client_secret'))) {
            Log::warning('Paymob intention creation failed.', [
                'payment_code' => $payment->code,
                'status' => $response->status(),
            ]);

            throw new PaymentProviderUnavailableException($this->code());
        }

        $reference = (string) ($response->json('intention_order_id') ?? $response->json('id'));
        $payment->update(['provider_reference' => $reference]);

        $checkoutUrl = self::BASE_URL . '/unifiedcheckout/?' . http_build_query([
            'publicKey' => config('services.paymob.public_key'),
            'clientSecret' => $response->json('client_secret'),
        ]);

        return [
            'type' => 'redirect',
            'checkout_url' => $checkoutUrl,
        ];
    }

    /**
     * $headers = ['hmac' => <قيمة hmac من الـ query string>]
     */
    public function verifyWebhook(array $payload, array $headers): bool
    {
        $received = $headers['hmac'] ?? null;
        $secret = config('services.paymob.hmac_secret');
        $obj = $payload['obj'] ?? null;

        if (!$received || !$secret || !is_array($obj)) {
            return false;
        }

        $bool = fn($v) => is_bool($v) ? ($v ? 'true' : 'false') : (string) $v;

        $string = implode('', [
            data_get($obj, 'amount_cents'),
            data_get($obj, 'created_at'),
            data_get($obj, 'currency'),
            $bool(data_get($obj, 'error_occured')),
            $bool(data_get($obj, 'has_parent_transaction')),
            data_get($obj, 'id'),
            data_get($obj, 'integration_id'),
            $bool(data_get($obj, 'is_3d_secure')),
            $bool(data_get($obj, 'is_auth')),
            $bool(data_get($obj, 'is_capture')),
            $bool(data_get($obj, 'is_refunded')),
            $bool(data_get($obj, 'is_standalone_payment')),
            $bool(data_get($obj, 'is_voided')),
            data_get($obj, 'order.id'),
            data_get($obj, 'owner'),
            $bool(data_get($obj, 'pending')),
            data_get($obj, 'source_data.pan'),
            data_get($obj, 'source_data.sub_type'),
            data_get($obj, 'source_data.type'),
            $bool(data_get($obj, 'success')),
        ]);

        return hash_equals(hash_hmac('sha512', $string, $secret), (string) $received);
    }

    public function queryStatus(string $providerReference): array
    {
        // التأكيد الفعلي بيتم عن طريق الـ Webhook فقط
        return ['status' => 'pending'];
    }
}
