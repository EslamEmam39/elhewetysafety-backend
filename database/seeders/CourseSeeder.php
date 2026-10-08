<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\CourseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $safetyCategory = CourseCategory::query()->where('slug', 'occupational-safety')->firstOrFail();
        $fireCategory = CourseCategory::query()->where('slug', 'fire-safety')->firstOrFail();
        $instructor = User::query()->where('email', 'admin@elhewety.test')->first();
        $this->seedCourse(
            [
                'course_category_id' => $safetyCategory->id,
                'instructor_id' => $instructor?->id,
                'title' => 'Occupational Safety Fundamentals',
                'slug' => 'occupational-safety-fundamentals',
                'description' => 'A practical introduction to workplace hazard identification and control.',
                'price' => '1499.00',
                'currency' => 'EGP',
                'level' => 'beginner',
                'thumbnail_storage_key' => null,
                'status' => 'published',
                'published_at' => now()->subDays(10),
            ],
            [
                [
                    'code' => 'occupational-safety-foundations',
                    'title' => 'Safety Foundations',
                    'lessons' => [
                        ['code' => 'introduction-to-occupational-safety', 'title' => 'Introduction to Occupational Safety', 'duration_seconds' => 720, 'is_free_preview' => true],
                        ['code' => 'hazard-identification', 'title' => 'Hazard Identification', 'duration_seconds' => 1080, 'is_free_preview' => false],
                    ],
                ],
                [
                    'code' => 'occupational-risk-control',
                    'title' => 'Risk Control',
                    'lessons' => [
                        ['code' => 'risk-assessment-basics', 'title' => 'Risk Assessment Basics', 'duration_seconds' => 900, 'is_free_preview' => false],
                        ['code' => 'hierarchy-of-controls', 'title' => 'Hierarchy of Controls', 'duration_seconds' => 840, 'is_free_preview' => false],
                    ],
                ],
            ]
        );

        $this->seedCourse(
            [
                'course_category_id' => $fireCategory->id,
                'instructor_id' => $instructor?->id,
                'title' => 'Fire Safety and Evacuation',
                'slug' => 'fire-safety-and-evacuation',
                'description' => 'A free introductory course covering fire prevention and evacuation.',
                'price' => '0.00',
                'currency' => 'EGP',
                'level' => 'beginner',
                'thumbnail_storage_key' => null,
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ],
            [
                [
                    'code' => 'fire-safety-essentials',
                    'title' => 'Fire Safety Essentials',
                    'lessons' => [
                        ['code' => 'fire-triangle-and-prevention', 'title' => 'Fire Triangle and Prevention', 'duration_seconds' => 600, 'is_free_preview' => true],
                        ['code' => 'emergency-evacuation', 'title' => 'Emergency Evacuation', 'duration_seconds' => 780, 'is_free_preview' => true],
                    ],
                ],
            ]
        );

        $this->seedCourse([
            'course_category_id' => $safetyCategory->id,
            'instructor_id' => $instructor?->id,
            'title' => 'NEBOSH Preparation Draft',
            'slug' => 'nebosh-preparation-draft',
            'description' => 'A draft course used to confirm unpublished content is hidden.',
            'price' => '2499.00',
            'currency' => 'EGP',
            'level' => 'advanced',
            'thumbnail_storage_key' => null,
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    private function seedCourse(array $courseData, array $chapters = []): void
    {
        $course = Course::query()->updateOrCreate(['slug' => $courseData['slug']], $courseData);

        foreach ($chapters as $chapterIndex => $chapterData) {
            $chapter = Chapter::query()->updateOrCreate(
                ['course_id' => $course->id, 'sort_order' => $chapterIndex + 1],
                ['code' => $chapterData['code'], 'title' => $chapterData['title']]
            );

            foreach ($chapterData['lessons'] as $lessonIndex => $lessonData) {
                $lesson = Lesson::query()->updateOrCreate(
                    ['chapter_id' => $chapter->id, 'sort_order' => $lessonIndex + 1],
                    $lessonData
                );

                // Keep uploaded video metadata when the local demo data is re-seeded.
                if ($lesson->video_storage_key === null && $lesson->video_hls_manifest_key === null) {
                    $lesson->update([
                        'video_disk' => null,
                        'video_mime_type' => null,
                        'video_size_bytes' => null,
                        'video_status' => 'pending',
                    ]);
                }
            }
        }
    }
}
