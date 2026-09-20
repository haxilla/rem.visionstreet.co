<?php

namespace App\Mail;

use App\Models\Core\Propagent;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

// Sent after a password is set, so an agent who didn't do it finds out at once.
class AgentPasswordChangedMail extends Mailable
{
    public function __construct(public Propagent $agent) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your RealtyEmails password was changed');
    }

    public function content(): Content
    {
        return new Content(
            html: 'mail.password-changed',
            text: 'mail.password-changed-text',
        );
    }
}
