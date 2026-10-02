<?php

namespace App\Policies;

use App\Models\PaymentGateway;
use App\Models\User;

class PaymentGatewayPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('payment-gateways.view');
    }

    public function view(User $user, PaymentGateway $gateway): bool
    {
        return $user->checkPermissionTo('payment-gateways.view');
    }

    public function create(User $user): bool
    {
        return $user->checkPermissionTo('payment-gateways.manage');
    }

    public function update(User $user, PaymentGateway $gateway): bool
    {
        return $user->checkPermissionTo('payment-gateways.manage');
    }

    public function delete(User $user, PaymentGateway $gateway): bool
    {
        return $user->checkPermissionTo('payment-gateways.manage');
    }
}
