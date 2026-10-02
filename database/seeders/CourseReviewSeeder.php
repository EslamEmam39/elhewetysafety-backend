<?php
namespace Database\Seeders;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\User;
use Illuminate\Database\Seeder;
class CourseReviewSeeder extends Seeder { public function run(): void { if(!app()->environment('local'))return; $user=User::where('email','student@elhewety.test')->firstOrFail(); $course=Course::where('slug','fire-safety-and-evacuation')->firstOrFail(); CourseReview::updateOrCreate(['user_id'=>$user->id,'course_id'=>$course->id],['code'=>'REV-DEMO-FIRE','rating'=>5,'review_text'=>'Clear and practical introduction to fire safety.','hidden_at'=>null]); } }
