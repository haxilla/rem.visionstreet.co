{{-- resources/views/home/hero.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full bg-white text-gray-900">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $pageTitle ?? 'Realty Emails' }}</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

  {{-- Modern fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Fraunces:opsz,wght@9..144,400;500;600&display=swap" rel="stylesheet">

  <style>
    :root{
      --font-sans: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
      --font-display: Fraunces, ui-serif, Georgia, Cambria, "Times New Roman", Times, serif;
    }
    body{
      font-family: var(--font-sans);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      text-rendering: geometricPrecision;
    }
    .font-display{ font-family: var(--font-display); }
  </style>
</head>

<body class="min-h-screen bg-white">
  @php
    $brandName   = $brandName   ?? 'RealtyEmails';
    $headline    = $headline    ?? 'Maximize Exposure<br>On Your Listing!';
    $subLines    = $subLines    ?? [
      'Easily Create Professional Real Estate',
      'E-Flyers with a FREE Website',
      'for YOU & Your Property!',
    ];
    $ctaText     = $ctaText     ?? 'Create FREE Flyer!';

    $heroMinH = $heroMinH ?? 'min-h-[520px]';
  @endphp

  {{-- White page, centered card --}}
  <div class="w-full bg-white px-4 sm:px-6 lg:px-8 py-6 lg:py-10">
    <div class="mx-auto w-full max-w-screen-2xl" style="max-width: 1600px;">
      <div class="grid grid-cols-1 lg:grid-cols-2 {{ $heroMinH }} overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-black/10">

        {{-- LEFT: Swiper (listings) --}}
        <div class="relative overflow-hidden bg-black">

          <div class="swiper h-full" data-swiper>
            <div class="swiper-wrapper">

              @foreach($newAdds as $the)
                @php
                  $photo = $the->thePhotos->where('def','=','1')->first()->photoName;
                  $listingImg = "https://realtyrepublic.com/hqphotos/{$the->theMeta->zipDir}/{$the->theMeta->mlsDir}/{$photo}";

                  $agentImg = null;
                  if ($the->theAgent->agtPhoto && $the->theAgent->theAgentCleanup) {
                    $agentImg = "https://realtyrepublic.com/agentPhotos/{$the->theAgent->theAgentCleanup->newRemID}/{$the->theAgent->agtPhoto}";
                  } elseif ($the->theAgent->agtPhoto) {
                    $agentImg = "https://realtyemails.com/HQoffice/{$the->theOffice->officeID}/{$the->theAgent->agtPhoto}";
                  }

                  $street   = $the->xFullStreet;
                  $cityLine = "{$the->xCity}, {$the->xState} {$the->xxZip}";
                  $agentName  = $the->theAgent->agtFullName;
                  $officeName = $the->theOffice->officeName;
                  $agentPhone = $the->theAgent->agtMainPhone;

                  $badgeText = $badgeText ?? 'Featured';
                @endphp

                <div class="swiper-slide">
                  <div class="relative {{ $heroMinH }}">

                    {{-- background photo --}}
                    <img
                      src="{{ $listingImg }}"
                      alt="{{ $street }}"
                      class="absolute inset-0 h-full w-full object-cover"
                    />

                    {{-- readability overlay --}}
                    <div class="absolute inset-0 bg-black/20"></div>

                    {{-- top-left listing info --}}
                    <div class="absolute left-6 top-6 z-10 text-white drop-shadow">
                      <div class="inline-flex items-center rounded-full bg-black/35 px-3 py-1 text-[11px] font-medium tracking-wide ring-1 ring-white/10 backdrop-blur-sm">
                        {{ $badgeText }}
                      </div>

                      <div class="mt-3 text-[15px] font-medium leading-snug">
                        {{ $street }}
                      </div>
                      <div class="mt-1 text-[12px] font-normal opacity-90">
                        {{ $cityLine }}
                      </div>
                    </div>

                    {{-- bottom-left agent card --}}
                    <div class="absolute bottom-6 left-6 z-10 flex items-center gap-3 rounded-xl bg-black/40 px-3.5 py-3 text-white backdrop-blur-sm ring-1 ring-white/10">
                      @if($agentImg)
                        <img
                          src="{{ $agentImg }}"
                          alt="{{ $agentName }}"
                          class="h-16 w-auto rounded-lg ring-1 ring-white/25 shadow-sm"
                        />
                      @endif

                      <div class="leading-tight">
                        <div class="text-[13px] font-semibold">
                          {{ $agentName }}
                        </div>
                        <div class="mt-0.5 text-[12px] font-normal opacity-85">
                          {{ $officeName }}
                        </div>
                        <div class="mt-1 text-[12px] font-normal opacity-85">
                          {{ $agentPhone }}
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              @endforeach

            </div>
          </div>
        </div>

        {{-- RIGHT: Marketing panel (shifted to BLUE/NAVY, less purple) --}}
        <div class="relative flex items-center justify-center overflow-hidden bg-gradient-to-b from-[#2f4f7f] via-[#2c3f73] to-[#1e2f57] px-8 py-14 text-white">
          {{-- subtle mid highlight band like original --}}
          <div class="pointer-events-none absolute inset-x-0 top-[45%] h-px bg-white/10"></div>

          <div class="w-full max-w-md text-center">

            {{-- Headline --}}
            <h1 class="font-display text-[34px] sm:text-[42px] font-medium tracking-tight leading-[1.05]">
              {!! $headline !!}
            </h1>

            {{-- Supporting lines --}}
            <div class="mt-8 space-y-2 text-[14px] sm:text-[15px] font-normal opacity-90">
              @foreach($subLines as $line)
                <div>{{ $line }}</div>
              @endforeach
            </div>

            {{-- CTA --}}
            <div class="mt-10">
              <a
                class="inline-flex items-center justify-center rounded-full border border-white/25 bg-white/10 px-8 py-3 text-[13px] font-semibold tracking-wide shadow-sm hover:bg-white/15 ring-1 ring-white/10 backdrop-blur-sm"
              >
                {{ $ctaText }}
              </a>
              <div class="mt-4 text-[12px] font-normal opacity-75">
                No credit card required • Launch in minutes
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- FEATURES SECTION (Themify Icons) --}}
  @php
    // Make colors match the "more blue / subtle gradient" reference
    $kicker = $kicker ?? 'Looking to do <span class="font-semibold text-[#214e9b]">MORE</span> than just MLS &amp; wait?';
    $title  = $title  ?? 'Get Your Listing Noticed!';
    $sub    = $sub    ?? 'Each flyer includes the following...';

    $items = $items ?? [
      [
        'icon'  => 'ti-email',
        'title' => 'EMAIL',
        'text'  => 'THOUSANDS Of Interested Local RE Agents.<br>(AZ &amp; NV)',
      ],
      [
        'icon'  => 'ti-comments',
        'title' => 'SHARE',
        'text'  => 'Easily Post to Social Media Sites (Facebook,<br>Twitter, etc.)',
      ],
      [
        'icon'  => 'ti-printer',
        'title' => 'PRINT',
        'text'  => 'Print Color Brochures with a Custom Link to<br>view your Online Flyer!',
      ],
      [
        'icon'  => 'ti-world',
        'title' => 'ONLINE',
        'text'  => 'Each Flyer Includes custom landing page with<br><span class="italic">YOUR</span> contact info',
      ],
      [
        'icon'  => 'ti-stats-up',
        'title' => 'TRACK',
        'text'  => 'Monitor the progress of your campaigns and<br>see how many times your flyer is viewed',
      ],
      [
        'icon'  => 'ti-bolt',
        'title' => 'AUTO IMPORT',
        'text'  => 'If its on the MLS or anywhere online, just enter<br>address or MLS# for instant creation',
      ],
    ];

    // subtle icon gradient (blue -> soft violet, no hot pink)
    $iconGradient = $iconGradient ?? 'bg-gradient-to-b from-[#2e56a3] via-[#416bc2] to-[#7a6cc8]';
    $headlineBlue = $headlineBlue ?? '#214e9b';
    $dividerColor = $dividerColor ?? 'bg-[#e6ebf6]';
  @endphp

  <section class="w-full bg-white">
    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-10 py-14 lg:py-16" style="max-width:1600px;">

      {{-- Header --}}
      <div class="text-center">
        <div class="text-[15px] sm:text-[16px] text-gray-800">
          {!! $kicker !!}
        </div>

        <h2 class="mt-3 text-[34px] sm:text-[46px] font-extrabold tracking-tight" style="color: {{ $headlineBlue }};">
          {{ $title }}
        </h2>

        <div class="mt-2 text-[14px] sm:text-[15px] text-gray-800">
          {{ $sub }}
        </div>

        {{-- squiggle --}}
        <div class="mt-5 flex justify-center">
          <span class="text-[22px] leading-none tracking-[0.22em] text-gray-900">~~~</span>
        </div>
      </div>

      {{-- Grid --}}
      <div class="mt-12 grid grid-cols-1 md:grid-cols-3">

        @foreach($items as $i => $it)
          @php
            $col = $i % 3;                  // 0,1,2
            $isSecondRow = $i >= 3;         // bottom row
            $needsLeftDivider = $col !== 0; // add vertical line on cols 2 & 3
          @endphp

          <div class="relative px-8 py-12 text-center">

            {{-- vertical dividers (desktop) --}}
            @if($needsLeftDivider)
              <div class="hidden md:block absolute left-0 top-8 bottom-8 w-px {{ $dividerColor }}"></div>
            @endif

            {{-- horizontal divider between the two rows --}}
            @if($isSecondRow)
              <div class="absolute left-8 right-8 top-0 h-px {{ $dividerColor }}"></div>
            @endif

            {{-- Icon (subtle blue gradient, no neon pink) --}}
            <div class="mx-auto flex h-16 w-16 items-center justify-center">
              <i class="{{ $it['icon'] }} text-[56px] text-transparent bg-clip-text {{ $iconGradient }}"></i>
            </div>

            {{-- Title --}}
            <div class="mt-5 text-[18px] font-extrabold tracking-wide" style="color: {{ $headlineBlue }};">
              {{ $it['title'] }}
            </div>

            {{-- Text --}}
            <div class="mt-3 text-[14px] leading-relaxed text-gray-900">
              {!! $it['text'] !!}
            </div>

          </div>
        @endforeach

      </div>

      {{-- Footer line --}}
      <div class="mt-14 text-center">
        <div class="text-[20px] font-extrabold tracking-wide" style="color: {{ $headlineBlue }};">
          PROVIDING AN EASY WAY
        </div>
        <div class="mt-1 text-[14px]" style="color: {{ $headlineBlue }};">
          to help you go beyond the average agent
        </div>
      </div>

    </div>
  </section>

</body>
</html>