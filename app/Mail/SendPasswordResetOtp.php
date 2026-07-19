<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendPasswordResetOtp extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $email,
        public int $otp,
        public string $resetUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Reset Password Anda',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset-otp',
            with: [
                'otp' => $this->otp,
                'resetUrl' => $this->resetUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
