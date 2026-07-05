<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Delivers the one-time password code to the user's email address. */
class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly string $code) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('messages.auth.otp_mail_subject'));
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp-code',
            with: ['code' => $this->code],
        );
    }
}
