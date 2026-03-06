{{-- resources/views/home/hero.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full bg-white text-gray-900">
@include('public.layout.head');

<body class="min-h-screen bg-white pt-[50px]">
  @include('public.layout.nav')
  <section>
    @include('public.includes.hero_card')
  </section>
  <section>
    @include('public.includes.features_section')
  </section>
  <section class="bg-[#f5f5f7] py-12 lg:py-16">
    @include('public.includes.top_views_3')
  </section>
  <section>

  @php
    $tilePattern = [
        'tile-lg-portrait',
        'tile-square',
        'tile-square',
        'tile-square',
        'tile-portrait',
        'tile-wide',
        'tile-square',
        'tile-square',
        'tile-portrait',
        'tile-square',
        'tile-square',
        'tile-lg-portrait',
    ];
@endphp

<div class="agent-mosaic-luxury">
    @foreach($memberSince as $index => $agent)
        @php
            $tileClass = $tilePattern[$index % count($tilePattern)];

            $folder = $agent->theAgentMeta->newRemID
                ?? $agent->theAgentCleanup->newRemID
                ?? null;

            $photo = ($folder && !empty($agent->agtPhoto))
                ? "https://realtyrepublic.com/agentPhotos/{$folder}/{$agent->agtPhoto}"
                : null;
        @endphp

        @if($photo)
            <div class="agent-tile {{ $tileClass }}">
                <img
                    src="{{ $photo }}"
                    alt="{{ $agent->agtFullName }}"
                    loading="lazy"
                    decoding="async"
                >
            </div>
        @endif
    @endforeach
</div>

  </section>


</body>
</html>