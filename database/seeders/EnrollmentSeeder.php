<?php
namespace Database\Seeders;
use App\Enums\EnrollmentSource;
use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;
class EnrollmentSeeder extends Seeder
{
    public function run(): void { if(!app()->environment('local'))return; $user=User::where('email','student@elhewety.test')->firstOrFail(); $course=Course::where('slug','fire-safety-and-evacuation')->firstOrFail(); Enrollment::updateOrCreate(['user_id'=>$user->id,'course_id'=>$course->id],['code'=>'ENR-DEMO-FREE','payment_id'=>null,'source'=>EnrollmentSource::Free,'status'=>EnrollmentStatus::Active,'enrolled_at'=>now(),'revoked_at'=>null]); }
}
