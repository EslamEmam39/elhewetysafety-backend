<?php
namespace App\Policies;
use App\Models\CourseReview;
use App\Models\User;
class CourseReviewPolicy { public function viewAny(User $user): bool { return $user->checkPermissionTo('course-reviews.view'); } public function view(User $user,CourseReview $review): bool { return $user->checkPermissionTo('course-reviews.view'); } public function manage(User $user,CourseReview $review): bool { return $user->checkPermissionTo('course-reviews.manage'); } }
