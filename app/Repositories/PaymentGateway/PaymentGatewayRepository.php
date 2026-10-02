<?php

namespace App\Repositories\PaymentGateway;

use App\Models\PaymentGateway;
use Illuminate\Database\Eloquent\Collection;

class PaymentGatewayRepository
{
    public function active(): Collection
    {
        return PaymentGateway::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function all(): Collection
    {
        return PaymentGateway::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function create(array $data): PaymentGateway
    {
        return PaymentGateway::create($data);
    }

    public function update(PaymentGateway $gateway, array $data): PaymentGateway
    {
        $gateway->update($data);

        return $gateway->refresh();
    }

    public function deactivate(PaymentGateway $gateway): PaymentGateway
    {
        $gateway->update(['is_active' => false]);

        return $gateway->refresh();
    }
}
