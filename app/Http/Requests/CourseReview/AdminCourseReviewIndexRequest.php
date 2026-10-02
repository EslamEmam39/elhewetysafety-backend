<?php
namespace App\Http\Requests\CourseReview;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;
class AdminCourseReviewIndexRequest extends ApiFormRequest { public function rules(): array { return ['visibility' => ['sometimes', Rule::in(['visible','hidden'])], 'course_slug' => ['sometimes','string','max:255'], 'rating' => ['sometimes','integer','between:1,5'], 'per_page' => ['sometimes','integer','min:1','max:100']]; } }
