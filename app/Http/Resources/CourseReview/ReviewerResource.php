<?php
namespace App\Http\Resources\CourseReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class ReviewerResource extends JsonResource { public function toArray(Request $request): array { return ['uuid'=>$this->uuid,'full_name_en'=>$this->full_name_en,'full_name_ar'=>$this->full_name_ar,'avatar_storage_key'=>$this->whenLoaded('student',fn()=>$this->student?->avatar_storage_key)]; } }
