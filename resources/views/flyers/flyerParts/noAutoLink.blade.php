{{-- Prints $text (a street address, or a city / state / zip) so an email program doesn't turn
     it into a link. Gmail and Apple Mail spot anything that looks like a street address and
     show it as a blue underlined map link, whatever colour the flyer gives it. In an email
     ($display == 'email') an invisible zero-width space goes in front of every word, which
     stops it being recognised as an address; on screen the text prints exactly as before.
     Needs $text; reads $display from the template that includes it. --}}
@php $__t = trim(preg_replace('/\s+/', ' ', (string) ($text ?? ''))); @endphp
{!! (($display ?? 'screen') === 'email' && $__t !== '') ? implode(' ', array_map(fn ($w) => '&#8203;' . e($w), explode(' ', $__t))) : e($__t) !!}
