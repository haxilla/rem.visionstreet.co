{{-- Prints $text (a street address, or a city / state / zip) so an email program doesn't turn
     it into a blue underlined map link. Gmail and Apple Mail spot anything that looks like a
     street address and link it themselves, in their own colour, whatever the flyer styles it
     as - and invisible characters inside the text did not stop Gmail.
     What does stop it: text that is ALREADY inside a link isn't linked a second time. So in an
     email ($display == 'email') the text goes in a link to the flyer's own page, styled to
     look like plain text - the header's text colour ($color, else inherited) and no underline.
     On screen, and when the flyer has no url_slug yet, the text prints exactly as before.
     Needs $text; optional $color (e.g. '#ffffff'); reads $display, $fromURL and $propInfo
     from the template that includes it. --}}
@php
    $__t    = trim(preg_replace('/\s+/', ' ', (string) ($text ?? '')));
    $__slug = $propInfo->url_slug ?? null;
@endphp
@if(($display ?? 'screen') === 'email' && $__t !== '' && $__slug)<a href="{{ $fromURL ?? '' }}/homedetails/{{ $__slug }}" target="_blank" style="color:{{ $color ?? 'inherit' }};text-decoration:none;font-weight:inherit;">{{ $__t }}</a>@else{{ $__t }}@endif
