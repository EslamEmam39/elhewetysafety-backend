<?php
namespace App\Http\Requests\Enrollment;
use App\Enums\EnrollmentSource;
use App\Enums\EnrollmentStatus;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;
class AdminEnrollmentIndexRequest extends ApiFormRequest { public function rules(): array { return ['status'=>['sometimes',Rule::enum(EnrollmentStatus::class)],'source'=>['sometimes',Rule::enum(EnrollmentSource::class)],'user_uuid'=>['sometimes','uuid'],'course_slug'=>['sometimes','string','max:255'],'per_page'=>['sometimes','integer','min:1','max:100']]; } }
