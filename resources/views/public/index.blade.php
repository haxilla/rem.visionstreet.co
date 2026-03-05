<!DOCTYPE html>
<html lang="en" class="h-full bg-black text-white">

  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Realty Emails</title>

      {{-- Vite asset bundling --}}
      @vite(['resources/css/app.css', 'resources/js/app.js'])

      {{-- CSRF token for forms --}}
      <meta name="csrf-token" content="{{ csrf_token() }}">

      {{-- Optional: favicon --}}
      <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

  </head>
  <body>
    <div class="swiper" data-swiper>
      <div class="swiper-wrapper">
        @foreach($newAdds as $the)
<div class="swiper-slide relative h-[420px]">

    <!-- Background listing photo -->
    <img
        class="absolute inset-0 w-full h-full object-cover"
        src="https://www.realtyrepublic.com/hqphotos/{{$the->theMeta->zipDir}}/{{$the->theMeta->mlsDir}}/{{$the->thePhotos->where('def','=','1')->first()->photoName}}"
        alt="{{$the->xFullStreet}}"
    >

    <!-- optional dark gradient overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>

    <!-- CONTENT OVERLAY -->
    <div class="absolute bottom-0 left-0 right-0 p-6 text-white">

        <div class="text-sm uppercase tracking-wide mb-1">
            Featured
        </div>

        <div class="text-xl font-semibold">
            {{$the->xFullStreet}}
        </div>

        <div class="text-sm opacity-90">
            {{$the->xCity}}, {{$the->xState}} {{$the->xxZip}}
        </div>

        <!-- Agent -->
        <div class="flex items-center gap-3 mt-4">

            <img
                class="w-12 h-12 rounded-full object-cover border-2 border-white"
                src='https://www.realtyrepublic.com/agentPhotos/{{$the->theAgent->theAgentCleanup->newRemID ?? ""}}/{{$the->theAgent->agtPhoto}}'
            >

            <div>
                <div class="font-semibold">
                    {{$the->theAgent->agtFullName}}
                </div>

                <div class="text-sm opacity-90">
                    {{$the->theOffice->officeName}}
                </div>

                <div class="text-sm opacity-90">
                    {{$the->theAgent->agtMainPhone}}
                </div>
            </div>

        </div>

    </div>

</div>
        @endforeach
        <!-- END LOOP 1 -->
      </div>
      <div class="swiper-pagination"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
    </div>
  </body>

</html>
