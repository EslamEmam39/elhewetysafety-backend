<?php

namespace App\Http\Requests\Course;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes','string','max:255'], 'slug' => ['sometimes','string','max:255','alpha_dash',Rule::unique('courses','slug')->ignore($this->route('course'))],
            'description' => ['sometimes','nullable','string'], 'price' => ['sometimes','decimal:0,2','min:0'], 'currency' => ['sometimes','string','size:3'],
            'level' => ['sometimes',Rule::in(['beginner','intermediate','advanced'])], 'status' => ['sometimes',Rule::in(['draft','published'])],
            'thumbnail_storage_key' => ['sometimes','nullable','string','max:2048'], 'course_category_slug' => ['sometimes','string','exists:course_categories,slug'],
            'instructor_uuid' => ['sometimes','nullable','uuid','exists:users,uuid'],
        ];
    }
}
