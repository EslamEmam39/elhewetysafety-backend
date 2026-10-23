<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use App\Services\Payment\Gateways\PaymobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymobWebhookController extends Controller
{
    public function __construct(
        private readonly PaymobService $paymob,
        private readonly PaymentService $paymentService,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (!$this->paymob->verifyWebhook($payload, ['hmac' => $request->query('hmac')])) {
            Log::warning('Paymob webhook: invalid HMAC');
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        if (($payload['type'] ?? null) !== 'TRANSACTION' || ! is_array($payload['obj'] ?? null)) {
            return response()->json(['message' => 'Ignored']);
        }

        $this->paymentService->completePaymobWebhook($payload['obj']);

        // لازم نرد 200 عشان Paymob متعيدش الإرسال
        return response()->json(['message' => 'OK']);
    }
}
