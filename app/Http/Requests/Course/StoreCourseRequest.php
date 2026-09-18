<?php

namespace App\Http\Requests\Course;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required','string','max:255'], 'slug' => ['required','string','max:255','alpha_dash',Rule::unique('courses','slug')],
            'description' => ['nullable','string'], 'price' => ['required','decimal:0,2','min:0'], 'currency' => ['required','string','size:3'],
            'level' => ['required',Rule::in(['beginner','intermediate','advanced'])], 'status' => ['required',Rule::in(['draft','published'])],
            'thumbnail_storage_key' => ['nullable','string','max:2048'], 'course_category_slug' => ['required','string','exists:course_categories,slug'],
            'instructor_uuid' => ['nullable','uuid','exists:users,uuid'],
        ];
    }
}
