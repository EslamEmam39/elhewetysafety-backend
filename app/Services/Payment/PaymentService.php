<?php
namespace App\Services\Payment;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\Payment\CashPaymentCannotBeConfirmedException;
use App\Exceptions\Payment\OrderNotPayableException;
use App\Exceptions\Payment\PaymentEnrollmentAlreadyExistsException;
use App\Exceptions\Payment\PaymentGatewayUnavailableException;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\Enrollment\EnrollmentRepository;
use App\Repositories\Payment\PaymentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class PaymentService
{
    public function __construct(
        private readonly PaymentRepository $repository,
        private readonly EnrollmentRepository $enrollmentRepository,
        private readonly PaymentGatewayFactory $factory,
    ) {
    }

    /** @return array{payment: Payment, next_action: array} */
    public function createAttempt(User $user, Order $order, string $gatewayCode): array
    {
        $gateway = $this->repository->activeGateway($gatewayCode)
            ?? throw new PaymentGatewayUnavailableException;
        $gatewayService = $this->factory->make($gateway->code);
        $gatewayService->assertConfigured();

        $payment = DB::transaction(function () use ($user, $order, $gateway): Payment {
            $order = $this->repository->lockOwnedOrder($user, $order) ?? abort(404);
            if ($order->status !== OrderStatus::Pending) {
                throw new OrderNotPayableException;
            }

            return $this->repository->create(
                $order,
                $gateway,
                'PAY-' . Str::upper((string) Str::ulid()),
                (string) Str::ulid()
            );
        });

        try {
            $nextAction = $gatewayService->initiate($payment);
        } catch (\Throwable $exception) {
            DB::transaction(function () use ($payment): void {
                $lockedPayment = $this->repository->lockByCode($payment->code);

                if ($lockedPayment?->status === PaymentStatus::Pending) {
                    $this->repository->markFailed($lockedPayment, 'Unable to initialize payment provider.');
                }
            });

            throw $exception;
        }

        return ['payment' => $payment->refresh()->load('gateway'), 'next_action' => $nextAction];
    }
    public function attempts(User $user, Order $order): Collection
    {
        return $this->repository->forOwnedOrder($user, $order);
    }
    public function payment(User $user, Payment $payment): Payment
    {
        return $this->repository->findForUser($user, $payment) ?? abort(404);
    }
    public function adminPayments(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginateAdmin($filters);
    }
    public function adminPayment(Payment $payment): Payment
    {
        return $this->repository->loadAdmin($payment);
    }

    public function confirmCash(User $admin, Payment $payment): Payment
    {
        return DB::transaction(function () use ($admin, $payment): Payment {
            $payment = $this->repository->lockForCashConfirmation($payment) ?? abort(404);
            $order = $this->repository->lockOrder($payment->order) ?? abort(404);

            if (
                $payment->gateway->code !== 'cash'
                || $payment->status !== PaymentStatus::Pending
                || $order->status !== OrderStatus::Pending
            ) {
                throw new CashPaymentCannotBeConfirmedException;
            }

            return $this->completeVerifiedPayment($payment, $order, $admin);
        });
    }

    /**
     * Processes a signed Paymob transaction callback. The caller must verify
     * its HMAC before passing its payload into this method.
     */
    public function completePaymobWebhook(array $transaction): void
    {
        $paymentCode = data_get($transaction, 'order.merchant_order_id');

        if (blank($paymentCode)) {
            return;
        }

        DB::transaction(function () use ($paymentCode, $transaction): void {
            $payment = $this->repository->lockByCode((string) $paymentCode);

            if ($payment === null || $payment->gateway->code !== 'paymob') {
                return;
            }

            if ($payment->status !== PaymentStatus::Pending || data_get($transaction, 'pending') === true) {
                return;
            }

            $order = $this->repository->lockOrder($payment->order) ?? abort(404);
            $expectedCents = (int) round(((float) $payment->amount) * 100);
            $providerReference = (string) data_get($transaction, 'order.id');

            if (
                blank($providerReference)
                || $payment->provider_reference !== $providerReference
                || (int) data_get($transaction, 'amount_cents') !== $expectedCents
                || strtoupper((string) data_get($transaction, 'currency')) !== strtoupper($payment->currency)
            ) {
                $this->repository->markFailed($payment, 'Paymob callback details do not match the payment attempt.');
                return;
            }

            if (
                data_get($transaction, 'success') !== true
                || data_get($transaction, 'is_refunded') === true
                || data_get($transaction, 'is_voided') === true
                || $order->status !== OrderStatus::Pending
            ) {
                $this->repository->markFailed($payment, 'Paymob reported an unsuccessful payment.');
                return;
            }

            $this->completeVerifiedPayment($payment, $order);
        });
    }

    /**
     * Completes a verified provider result after the caller has locked both
     * the Payment and its Order inside the current transaction.
     */
    private function completeVerifiedPayment(Payment $payment, Order $order, ?User $cashConfirmedBy = null): Payment
    {
        if ($this->enrollmentRepository->existingForPaymentGrant($order->user, $order->course)) {
            throw new PaymentEnrollmentAlreadyExistsException;
        }

        $payment = $this->repository->markPaid($payment, $cashConfirmedBy);
        $this->repository->markOrderPaid($order);
        $this->enrollmentRepository->createFromPayment(
            $order->user,
            $order->course,
            $payment,
            'ENR-' . Str::upper((string) Str::ulid()),
        );

        return $this->repository->loadAdmin($payment);
    }
}
