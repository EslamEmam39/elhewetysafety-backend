<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Database\Seeder;

class LessonProgressSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $user = User::query()->where('email', 'student@elhewety.test')->firstOrFail();
        $lesson = Lesson::query()->where('code', 'fire-triangle-and-prevention')->firstOrFail();

        LessonProgress::query()->updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['last_position_seconds' => 120, 'last_watched_at' => now(), 'completed_at' => null]
        );
    }
}
