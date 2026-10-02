<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            TargetedProfessionalFieldSeeder::class,
            LocationSeeder::class,
            DemoUserSeeder::class,
            CourseCategorySeeder::class,
            CourseSeeder::class,
            PaymentGatewaySeeder::class,
            OrderSeeder::class,
            PaymentSeeder::class,
            EnrollmentSeeder::class,
            DeviceSeeder::class,
            LessonProgressSeeder::class,
            CourseReviewSeeder::class,
        ]);
    }
}
