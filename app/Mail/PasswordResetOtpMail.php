<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Delivers the password-reset OTP code to the user's email address. */
class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly string $otp) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('messages.auth.password_reset_otp_mail_subject'));
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset-otp',
            with: ['code' => $this->otp],
        );
    }
}
