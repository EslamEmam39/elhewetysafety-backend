<?php

namespace App\Services\PaymentGateway;

use App\Models\PaymentGateway;
use App\Repositories\PaymentGateway\PaymentGatewayRepository;
use Illuminate\Database\Eloquent\Collection;

class PaymentGatewayService
{
    public function __construct(private readonly PaymentGatewayRepository $repository)
    {
    }

    public function activeGateways(): Collection
    {
        return $this->repository->active();
    }

    public function gateways(): Collection
    {
        return $this->repository->all();
    }

    public function create(array $data): PaymentGateway
    {
        return $this->repository->create($data);
    }

    public function update(PaymentGateway $gateway, array $data): PaymentGateway
    {
        return $this->repository->update($gateway, $data);
    }

    public function deactivate(PaymentGateway $gateway): PaymentGateway
    {
        return $this->repository->deactivate($gateway);
    }
}
