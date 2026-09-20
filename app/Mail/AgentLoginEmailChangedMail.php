<?php

namespace App\Mail;

use App\Models\Core\Propagent;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

// Sent to the OLD login address when an admin moves an account to a new one, so that if
// somebody other than the agent asked for it, the real owner finds out.
class AgentLoginEmailChangedMail extends Mailable
{
    public function __construct(
        public Propagent $agent,
        public string $oldEmail,
        public string $newEmailMasked,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'The login email on your RealtyEmails account was changed');
    }

    public function content(): Content
    {
        return new Content(
            html: 'mail.login-email-changed',
            text: 'mail.login-email-changed-text',
        );
    }
}
