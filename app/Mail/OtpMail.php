<?php

namespace App\Mail;

use App\Models\OtpCode;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $code,
        public readonly string $purpose,
        public readonly int $expirationMinutes
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->purpose === OtpCode::PURPOSE_EMAIL_VERIFICATION
                ? 'Verify your email address'
                : 'Reset your password'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp'
        );
    }
}
