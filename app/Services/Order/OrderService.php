<?php

namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Exceptions\Order\CourseUnavailableForOrderException;
use App\Exceptions\Order\FreeCourseDoesNotRequireOrderException;
use App\Exceptions\Order\OrderCannotBeCancelledException;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Order\OrderRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(private readonly OrderRepository $repository)
    {
    }

    public function create(User $user, string $courseSlug): Order
    {
        return DB::transaction(function () use ($user, $courseSlug): Order {
            $course = $this->repository->findPublishedCourseForUpdate($courseSlug)
                ?? throw new CourseUnavailableForOrderException;

            if (bccomp((string) $course->price, '0.00', 2) === 0) {
                throw new FreeCourseDoesNotRequireOrderException;
            }

            return $this->repository->findPending($user, $course)
                ?? $this->repository->create($user, $course, $this->generateCode());
        });
    }

    public function userOrders(User $user, array $filters): LengthAwarePaginator
    {
        return $this->repository->paginateForUser($user, $filters);
    }

    public function userOrder(User $user, Order $order): Order
    {
        return $this->repository->findForUser($user, $order) ?? abort(404);
    }

    public function cancel(User $user, Order $order): Order
    {
        $order = $this->userOrder($user, $order);

        if ($order->status !== OrderStatus::Pending) {
            throw new OrderCannotBeCancelledException;
        }

        return $this->repository->cancel($order);
    }

    public function adminOrders(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($filters);
    }

    public function adminOrder(Order $order): Order
    {
        return $this->repository->loadForAdmin($order);
    }

    private function generateCode(): string
    {
        return 'ORD-'.Str::upper((string) Str::ulid());
    }
}
