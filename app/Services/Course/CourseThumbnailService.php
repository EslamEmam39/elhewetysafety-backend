<?php

namespace App\Services\Course;

use App\Models\Course;
use App\Repositories\Course\AdminCourseRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class CourseThumbnailService
{
    public function __construct(private readonly AdminCourseRepository $repository) {}

    public function upload(Course $course, UploadedFile $thumbnail): Course
    {
        $disk = (string) config('course.thumbnail_disk');
        $storage = Storage::disk($disk);
        $extension = $thumbnail->guessExtension() ?: 'jpg';
        $key = $storage->putFileAs(
            "courses/{$course->slug}/thumbnails",
            $thumbnail,
            Str::uuid().'.'.Str::lower($extension),
        );

        if ($key === false) {
            throw new RuntimeException('The course thumbnail could not be uploaded.');
        }

        $previousKey = $course->thumbnail_storage_key;

        try {
            $updated = $this->repository->updateCourse($course, ['thumbnail_storage_key' => $key]);
        } catch (Throwable $exception) {
            $storage->delete($key);

            throw $exception;
        }

        if ($previousKey && $previousKey !== $key) {
            $storage->delete($previousKey);
        }

        return $updated;
    }

    public function delete(Course $course): void
    {
        if ($course->thumbnail_storage_key) {
            Storage::disk((string) config('course.thumbnail_disk'))->delete($course->thumbnail_storage_key);
        }
    }
}
