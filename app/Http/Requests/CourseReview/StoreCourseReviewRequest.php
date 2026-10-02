<?php
namespace App\Http\Requests\CourseReview;
use App\Http\Requests\ApiFormRequest;
class StoreCourseReviewRequest extends ApiFormRequest { public function rules(): array { return ['rating' => ['required', 'integer', 'between:1,5'], 'review_text' => ['nullable', 'string', 'max:2000']]; } }
