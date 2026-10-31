<?php

namespace App\Services\Certificate;

use ArPHP\I18N\Arabic;
use App\Exceptions\Certificate\CertificateAlreadyIssuedException;
use App\Models\Certificate;
use App\Models\User;
use App\Repositories\Certificate\CertificateRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class CertificateService
{
    public function __construct(private readonly CertificateRepository $repository) {}

    public function issue(User $issuer, array $data): Certificate
    {
        $recipient = $this->repository->recipientByUuid($data['user_uuid']) ?? abort(404);
        $course = $this->repository->courseBySlug($data['course_slug']) ?? abort(404);

        if ($this->repository->existing($recipient, $course)) {
            throw new CertificateAlreadyIssuedException;
        }

        $code = 'CER-'.Str::upper((string) Str::ulid());
        $disk = (string) config('certificate.disk');
        $storageKey = "certificates/{$code}.pdf";
        $pdf = $this->renderPdf($code, $recipient->full_name_en, $recipient->full_name_ar, $course->title);

        if (! Storage::disk($disk)->put($storageKey, $pdf)) {
            abort(500, 'The certificate PDF could not be stored.');
        }

        try {
            return DB::transaction(function () use ($issuer, $recipient, $course, $code, $disk, $storageKey): Certificate {
                if ($this->repository->existing($recipient, $course, true)) {
                    throw new CertificateAlreadyIssuedException;
                }

                return $this->repository->create([
                    'code' => $code,
                    'user_id' => $recipient->id,
                    'course_id' => $course->id,
                    'issued_by_user_id' => $issuer->id,
                    'recipient_full_name_en' => $recipient->full_name_en,
                    'recipient_full_name_ar' => $recipient->full_name_ar,
                    'course_title' => $course->title,
                    'pdf_disk' => $disk,
                    'pdf_storage_key' => $storageKey,
                    'issued_at' => now(),
                ]);
            });
        } catch (Throwable $exception) {
            Storage::disk($disk)->delete($storageKey);
            throw $exception;
        }
    }

    public function adminCertificates(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginateAdmin($filters);
    }

    public function adminCertificate(Certificate $certificate): Certificate
    {
        return $this->repository->loadAdmin($certificate);
    }

    public function verify(Certificate $certificate): Certificate
    {
        return $this->repository->publicCertificate($certificate);
    }

    public function regeneratePdf(Certificate $certificate): Certificate
    {
        $pdf = $this->renderPdf(
            $certificate->code,
            $certificate->recipient_full_name_en,
            $certificate->recipient_full_name_ar,
            $certificate->course_title,
        );

        abort_unless(Storage::disk($certificate->pdf_disk)->put($certificate->pdf_storage_key, $pdf), 500, 'The certificate PDF could not be stored.');

        return $this->repository->loadAdmin($certificate->refresh());
    }

    public function download(Certificate $certificate): StreamedResponse
    {
        abort_unless(Storage::disk($certificate->pdf_disk)->exists($certificate->pdf_storage_key), 404);

        return Storage::disk($certificate->pdf_disk)->download(
            $certificate->pdf_storage_key,
            "{$certificate->code}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }

    private function renderPdf(string $code, string $recipientNameEn, string $recipientNameAr, string $courseTitle): string
    {
        $options = new Options;
        $options->setDefaultFont('DejaVu Sans');
        $options->setIsRemoteEnabled(false);

        // Dompdf embeds Arabic glyphs but does not perform Arabic joining or
        // bidirectional shaping. Convert the snapshot to presentation glyphs
        // before rendering so the certificate reads correctly.
        $arabic = new Arabic;
        $recipientNameAr = $arabic->utf8Glyphs($recipientNameAr, 100, false, true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('certificates.course-certificate', compact('code', 'recipientNameEn', 'recipientNameAr', 'courseTitle'))->render(), 'UTF-8');
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->output();
    }
}
