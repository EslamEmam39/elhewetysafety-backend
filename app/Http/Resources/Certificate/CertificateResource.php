<?php

namespace App\Http\Resources\Certificate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'recipient_full_name_en' => $this->recipient_full_name_en,
            'recipient_full_name_ar' => $this->recipient_full_name_ar,
            'course_title' => $this->course_title,
            'course_slug' => $this->relationLoaded('course') ? $this->course?->slug : null,
            'issued_at' => $this->issued_at,
            'verification_url' => route('certificates.verify', $this),
            'pdf_url' => route('certificates.pdf', $this),
        ];
    }
}
