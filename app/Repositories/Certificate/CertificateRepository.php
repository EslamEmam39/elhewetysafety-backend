<?php

namespace App\Repositories\Certificate;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CertificateRepository
{
    public function recipientByUuid(string $uuid): ?User
    {
        return User::query()->where('uuid', $uuid)->first();
    }

    public function courseBySlug(string $slug): ?Course
    {
        return Course::query()->where('slug', $slug)->first();
    }

    public function existing(User $recipient, Course $course, bool $lock = false): ?Certificate
    {
        $query = Certificate::query()->whereBelongsTo($recipient, 'recipient')->whereBelongsTo($course);

        return $lock ? $query->lockForUpdate()->first() : $query->first();
    }

    public function create(array $attributes): Certificate
    {
        return Certificate::create($attributes)->load(['recipient', 'course', 'issuer']);
    }

    public function paginateAdmin(array $filters): LengthAwarePaginator
    {
        return Certificate::query()
            ->with(['recipient', 'course', 'issuer'])
            ->when($filters['user_uuid'] ?? null, fn ($query, string $uuid) => $query->whereHas('recipient', fn ($user) => $user->where('uuid', $uuid)))
            ->when($filters['course_slug'] ?? null, fn ($query, string $slug) => $query->whereHas('course', fn ($course) => $course->where('slug', $slug)))
            ->latest('issued_at')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function loadAdmin(Certificate $certificate): Certificate
    {
        return $certificate->loadMissing(['recipient', 'course', 'issuer']);
    }

    public function publicCertificate(Certificate $certificate): Certificate
    {
        return $certificate->loadMissing('course');
    }
}
