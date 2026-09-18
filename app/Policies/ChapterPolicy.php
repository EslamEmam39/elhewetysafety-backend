<?php
namespace App\Policies;
use App\Models\Chapter;
use App\Models\User;
class ChapterPolicy
{
    public function viewAny(User $user): bool { return $user->checkPermissionTo('courses.view'); }
    public function view(User $user, Chapter $chapter): bool { return $user->checkPermissionTo('courses.view'); }
    public function create(User $user): bool { return $user->checkPermissionTo('chapters.manage'); }
    public function update(User $user, Chapter $chapter): bool { return $user->checkPermissionTo('chapters.manage'); }
    public function delete(User $user, Chapter $chapter): bool { return $user->checkPermissionTo('chapters.manage'); }
}
