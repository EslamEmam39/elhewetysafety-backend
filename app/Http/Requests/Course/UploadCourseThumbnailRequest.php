<?php

namespace App\Http\Requests\Course;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rules\File;

class UploadCourseThumbnailRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'thumbnail' => [
                'required',
                File::image()
                    ->types(['jpeg', 'png', 'webp'])
                    ->max((int) config('course.thumbnail_upload_max_kilobytes')),
            ],
        ];
    }
}
