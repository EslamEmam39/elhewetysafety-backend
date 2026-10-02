<?php
namespace App\Http\Requests\CourseReview;
use App\Http\Requests\ApiFormRequest;
class UpdateCourseReviewRequest extends ApiFormRequest { public function rules(): array { return ['rating' => ['sometimes', 'integer', 'between:1,5'], 'review_text' => ['sometimes', 'nullable', 'string', 'max:2000']]; } }
