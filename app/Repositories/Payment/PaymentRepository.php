<?php
namespace App\Repositories\Payment;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
class PaymentRepository
{
    public function activeGateway(string $code): ?PaymentGateway { return PaymentGateway::query()->where('code', strtolower($code))->where('is_active', true)->first(); }
    public function lockOwnedOrder(User $user, Order $order): ?Order { return Order::query()->whereKey($order->id)->whereBelongsTo($user)->lockForUpdate()->first(); }
    public function create(Order $order, PaymentGateway $gateway, string $code, string $idempotencyKey): Payment
    {
        return Payment::create(['code'=>$code,'order_id'=>$order->id,'payment_gateway_id'=>$gateway->id,'amount'=>$order->amount,'currency'=>$order->currency,'status'=>PaymentStatus::Pending,'idempotency_key'=>$idempotencyKey])->load(['order.course','gateway']);
    }
    public function forOwnedOrder(User $user, Order $order): Collection { return Payment::query()->whereBelongsTo($order)->whereHas('order', fn($q)=>$q->where('user_id',$user->id))->with('gateway')->latest('id')->get(); }
    public function findForUser(User $user, Payment $payment): ?Payment { return Payment::query()->whereKey($payment->id)->whereHas('order', fn($q)=>$q->where('user_id',$user->id))->with(['order.course','gateway'])->first(); }
    public function paginateAdmin(array $filters): LengthAwarePaginator { return Payment::query()->with(['order.user.roles','order.course','gateway'])->when($filters['status']??null,fn($q,$v)=>$q->where('status',$v))->when($filters['gateway_code']??null,fn($q,$v)=>$q->whereHas('gateway',fn($q)=>$q->where('code',$v)))->latest('id')->paginate($filters['per_page']??15); }
    public function loadAdmin(Payment $payment): Payment { return $payment->loadMissing(['order.user.roles','order.course','gateway']); }
}
