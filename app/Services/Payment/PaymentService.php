<?php
namespace App\Services\Payment;
use App\Enums\OrderStatus;
use App\Exceptions\Payment\OrderNotPayableException;
use App\Exceptions\Payment\PaymentGatewayUnavailableException;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\Payment\PaymentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class PaymentService
{
    public function __construct(private readonly PaymentRepository $repository, private readonly PaymentGatewayFactory $factory) {}
    public function createAttempt(User $user, Order $order, string $gatewayCode): Payment
    {
        return DB::transaction(function() use($user,$order,$gatewayCode): Payment {
            $order=$this->repository->lockOwnedOrder($user,$order)??abort(404);
            if($order->status!==OrderStatus::Pending){throw new OrderNotPayableException;}
            $gateway=$this->repository->activeGateway($gatewayCode)??throw new PaymentGatewayUnavailableException;
            $this->factory->make($gateway->code);
            return $this->repository->create($order,$gateway,'PAY-'.Str::upper((string)Str::ulid()),(string)Str::ulid());
        });
    }
    public function attempts(User $user, Order $order): Collection { return $this->repository->forOwnedOrder($user,$order); }
    public function payment(User $user, Payment $payment): Payment { return $this->repository->findForUser($user,$payment)??abort(404); }
    public function adminPayments(array $filters): LengthAwarePaginator { return $this->repository->paginateAdmin($filters); }
    public function adminPayment(Payment $payment): Payment { return $this->repository->loadAdmin($payment); }
}
