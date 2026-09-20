Hi {{ trim((string) $agent->agtFirst) ?: 'there' }},

RealtyEmails has a new website. For your security you need to create a new password before you sign in - your old password can't be used.

Create your new password (link works once, for the next {{ $minutes }} minutes):
{{ $url }}

Didn't ask for this? You can ignore this email - nothing changes until someone follows the link.

RealtyEmails
