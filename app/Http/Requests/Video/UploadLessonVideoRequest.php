<?php

namespace App\Http\Requests\Video;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rules\File;

class UploadLessonVideoRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'video' => [
                'required',
                File::types(['mp4', 'webm', 'mov'])
                    ->max((int) config('video.upload_max_kilobytes')),
            ],
            'duration_seconds' => ['required', 'integer', 'min:1', 'max:86400'],
        ];
    }
}
