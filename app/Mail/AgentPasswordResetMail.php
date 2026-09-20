<?php

namespace App\Mail;

use App\Models\Core\Propagent;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

// The one-time "set your new password" link (see AgentPasswords::sendLink).
class AgentPasswordResetMail extends Mailable
{
    public function __construct(
        public Propagent $agent,
        public string $url,
        public int $minutes,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Set your new RealtyEmails password');
    }

    public function content(): Content
    {
        return new Content(
            html: 'mail.password-reset',
            text: 'mail.password-reset-text',
        );
    }
}
