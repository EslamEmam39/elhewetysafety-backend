<?php
namespace App\Policies;
use App\Models\Lesson;
use App\Models\User;
class LessonPolicy
{
    public function viewAny(User $user): bool { return $user->checkPermissionTo('courses.view'); }
    public function view(User $user, Lesson $lesson): bool { return $user->checkPermissionTo('courses.view'); }
    public function create(User $user): bool { return $user->checkPermissionTo('lessons.manage'); }
    public function update(User $user, Lesson $lesson): bool { return $user->checkPermissionTo('lessons.manage'); }
    public function delete(User $user, Lesson $lesson): bool { return $user->checkPermissionTo('lessons.manage'); }
}
