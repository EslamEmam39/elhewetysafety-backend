<?php
namespace App\Http\Resources\CourseReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class CourseReviewResource extends JsonResource { public function toArray(Request $request): array { return ['code'=>$this->code,'rating'=>$this->rating,'review_text'=>$this->review_text,'reviewer'=>ReviewerResource::make($this->whenLoaded('user')),'course_slug'=>$this->relationLoaded('course')?$this->course?->slug:null,'is_visible'=>$this->hidden_at===null,'hidden_at'=>$this->hidden_at,'created_at'=>$this->created_at,'updated_at'=>$this->updated_at]; } }
