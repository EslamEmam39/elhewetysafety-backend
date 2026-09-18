<?php
namespace App\Policies;
use App\Models\CourseCategory;
use App\Models\User;
class CourseCategoryPolicy
{
    public function viewAny(User $user): bool { return $user->checkPermissionTo('course-categories.view'); }
    public function view(User $user, CourseCategory $category): bool { return $user->checkPermissionTo('course-categories.view'); }
    public function create(User $user): bool { return $user->checkPermissionTo('course-categories.manage'); }
    public function update(User $user, CourseCategory $category): bool { return $user->checkPermissionTo('course-categories.manage'); }
    public function delete(User $user, CourseCategory $category): bool { return $user->checkPermissionTo('course-categories.manage'); }
}
