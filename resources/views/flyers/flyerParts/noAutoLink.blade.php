{{-- Prints $text (a street address, a city / state / zip, a phone number, an office name) so an email
     program doesn't turn it into a blue underlined map or phone link. Gmail and Apple Mail spot
     anything that looks like an address or a phone number and link it themselves, in their own
     colour, whatever the flyer styles it as - and invisible characters inside the text did not
     stop Gmail.
     What does stop it: text that is ALREADY inside a link isn't linked a second time. So in an
     email ($display == 'email') the text goes in a link of ours, styled to look like plain text -
     the colour given in $color (else inherited) and no underline.
     The link goes to $href when one is given (an agent's page, a tel: number), else to the flyer's
     own page when it has a url_slug, else to the site (a slug may be missing on an older flyer, so
     the wrapper must not depend on one). On screen the text prints exactly as before.
     Needs $text; optional $color (e.g. '#ffffff') and $href; reads $display, $fromURL and $propInfo
     from the template that includes it. --}}
@php
    $__t    = trim(preg_replace('/\s+/', ' ', (string) ($text ?? '')));
    $__slug = $propInfo->url_slug ?? null;
    $__href = !empty($href) ? $href : (($fromURL ?? '') . ($__slug ? '/homedetails/' . $__slug : ''));
@endphp
@if(($display ?? 'screen') === 'email' && $__t !== '')<a href="{{ $__href }}" @if(str_starts_with($__href, 'http')) target="_blank" @endif style="color:{{ $color ?? 'inherit' }};text-decoration:none;font-weight:inherit;">{{ $__t }}</a>@else{{ $__t }}@endif
