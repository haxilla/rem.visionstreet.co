{{-- resources/views/home/hero.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full bg-white text-gray-900">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $pageTitle ?? 'Realty Emails' }}</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
</head>

<body class="min-h-screen bg-white flex items-start justify-center">

@php
  $brandName   = $brandName   ?? 'RealtyEmails';

  $headline    = $headline ?? 'Maximize Exposure<br>On Your Listing!';

  $subLines    = $subLines ?? [
    'Easily Create Professional Real Estate',
    'E-Flyers with a FREE Website',
    'for YOU & Your Property!',
  ];

  $ctaText     = $ctaText ?? 'Create FREE Flyer!';

  $heroMinH = $heroMinH ?? 'min-h-[520px]';
@endphp


<div class="w-full max-w-[1600px] px-4 lg:px-6 py-6">

  <div class="grid grid-cols-1 lg:grid-cols-2 {{ $heroMinH }} overflow-hidden rounded-2xl shadow-lg ring-1 ring-black/10 bg-white">

    {{-- LEFT PANEL --}}
    <div class="relative overflow-hidden bg-black">

      {{-- Brand --}}
      <div class="absolute left-4 top-4 z-20 flex items-center gap-2 text-white">

        <button class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-black/40 backdrop-blur-sm">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
               fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                  stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>

        <div class="text-lg font-semibold tracking-wide">
          {{ $brandName }}
        </div>

      </div>


      <div class="swiper h-full" data-swiper>
        <div class="swiper-wrapper">

          @foreach($newAdds as $the)

          @php
            $photo = $the->thePhotos->where('def','=','1')->first()->photoName;

            $listingImg = "https://realtyrepublic.com/hqphotos/{$the->theMeta->zipDir}/{$the->theMeta->mlsDir}/{$photo}";

            $agentImg = null;

            if ($the->theAgent->agtPhoto && $the->theAgent->theAgentCleanup) {
              $agentImg = "https://realtyrepublic.com/agentPhotos/{$the->theAgent->theAgentCleanup->newRemID}/{$the->theAgent->agtPhoto}";
            }
            elseif ($the->theAgent->agtPhoto) {
              $agentImg = "https://realtyemails.com/HQoffice/{$the->theOffice->officeID}/{$the->theAgent->agtPhoto}";
            }

            $street   = $the->xFullStreet;
            $cityLine = "{$the->xCity}, {$the->xState} {$the->xxZip}";
            $agentName  = $the->theAgent->agtFullName;
            $officeName = $the->theOffice->officeName;
            $agentPhone = $the->theAgent->agtMainPhone;
          @endphp

          <div class="swiper-slide">

            <div class="relative {{ $heroMinH }}">

              {{-- Listing Photo --}}
              <img
                src="{{ $listingImg }}"
                alt="{{ $street }}"
                class="absolute inset-0 h-full w-full object-cover"
              />

              {{-- Dark overlay --}}
              <div class="absolute inset-0 bg-black/35"></div>

              {{-- Address --}}
              <div class="absolute left-6 top-20 z-10 text-white drop-shadow">

                <div class="text-sm font-semibold opacity-90">
                  Featured
                </div>

                <div class="mt-2 text-sm font-medium">
                  {{ $street }}
                </div>

                <div class="text-xs opacity-90">
                  {{ $cityLine }}
                </div>

              </div>


              {{-- Agent card --}}
              <div class="absolute bottom-6 left-6 z-10 flex items-center gap-3 rounded bg-black/40 px-3 py-2 text-white backdrop-blur-sm">

                @if($agentImg)

                <img
                  src="{{ $agentImg }}"
                  alt="{{ $agentName }}"
                  class="h-16 w-auto rounded-sm ring-1 ring-white/20
                >

                @endif

                <div class="leading-tight">

                  <div class="text-sm font-semibold">
                    {{ $agentName }}
                  </div>

                  <div class="text-xs opacity-90">
                    {{ $officeName }}
                  </div>

                  <div class="text-xs opacity-90">
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


    {{-- RIGHT PANEL --}}
    <div class="relative flex items-center justify-center bg-gradient-to-b from-indigo-900 via-indigo-800 to-indigo-950 px-8 py-14 text-white">

      <div class="w-full max-w-md text-center">

        <h1 class="text-3xl font-extrabold italic tracking-tight sm:text-4xl">
          {!! $headline !!}
        </h1>

        <div class="mt-10 space-y-2 text-sm opacity-95 sm:text-base">

          @foreach($subLines as $line)
            <div>{{ $line }}</div>
          @endforeach

        </div>

        <div class="mt-10">

          <a
            class="inline-flex items-center justify-center rounded-full border-2 border-yellow-300 bg-red-700 px-7 py-3 text-sm font-bold shadow hover:bg-red-600"
          >
            {{ $ctaText }}
          </a>

        </div>

      </div>

    </div>

  </div>

</div>

</body>
</html>