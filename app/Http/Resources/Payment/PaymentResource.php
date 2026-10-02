<?php
namespace App\Http\Resources\Payment;
use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\PaymentGateway\PaymentGatewayResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status->value,
            'gateway' => PaymentGatewayResource::make($this->whenLoaded('gateway')),
            'order' => OrderResource::make($this->whenLoaded('order')),
            'provider_reference' => $this->provider_reference,
            'failure_reason' => $this->failure_reason,
            'paid_at' => $this->paid_at,
            'refunded_at' => $this->refunded_at,
            'created_at' => $this->created_at
        ];
    }
}
