<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $user = User::query()->where('email', 'student@elhewety.test')->firstOrFail();
        $course = Course::query()->where('slug', 'occupational-safety-fundamentals')->firstOrFail();

        Order::query()->updateOrCreate(
            ['code' => 'ORD-DEMO-PENDING'],
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'amount' => $course->price,
                'currency' => $course->currency,
                'status' => OrderStatus::Pending,
            ]
        );
    }
}
