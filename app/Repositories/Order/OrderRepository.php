<?php

namespace App\Repositories\Order;

use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository
{
    public function findPublishedCourseForUpdate(string $slug): ?Course
    {
        return Course::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->lockForUpdate()
            ->first();
    }

    public function findPending(User $user, Course $course): ?Order
    {
        return Order::query()
            ->whereBelongsTo($user)
            ->whereBelongsTo($course)
            ->where('status', OrderStatus::Pending->value)
            ->with('course')
            ->latest('id')
            ->first();
    }

    public function create(User $user, Course $course, string $code): Order
    {
        return Order::create([
            'code' => $code,
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => $course->price,
            'currency' => strtoupper($course->currency),
            'status' => OrderStatus::Pending,
        ])->load('course');
    }

    public function paginateForUser(User $user, array $filters): LengthAwarePaginator
    {
        return Order::query()
            ->whereBelongsTo($user)
            ->with('course')
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->latest('id')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findForUser(User $user, Order $order): ?Order
    {
        return Order::query()
            ->whereKey($order->id)
            ->whereBelongsTo($user)
            ->with('course')
            ->first();
    }

    public function cancel(Order $order): Order
    {
        $order->update(['status' => OrderStatus::Cancelled]);

        return $order->refresh()->load('course');
    }

    public function paginateForAdmin(array $filters): LengthAwarePaginator
    {
        return Order::query()
            ->with(['course', 'user.roles'])
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['user_uuid'] ?? null, fn ($query, string $uuid) => $query->whereHas('user', fn ($query) => $query->where('uuid', $uuid)))
            ->when($filters['course_slug'] ?? null, fn ($query, string $slug) => $query->whereHas('course', fn ($query) => $query->where('slug', $slug)))
            ->latest('id')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function loadForAdmin(Order $order): Order
    {
        return $order->loadMissing(['course', 'user.roles']);
    }
}
