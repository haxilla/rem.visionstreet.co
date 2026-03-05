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
          <div class="swiper-slide">
            <div class="relative w-full aspect-[16/9] overflow-hidden rounded-2xl">

              {{-- background photo --}}
              <img
                src="https://www.realtyrepublic.com/hqphotos/{{$the->theMeta->zipDir}}/{{$the->theMeta->mlsDir}}/{{$the->thePhotos->where('def','=','1')->first()->photoName}}"
                alt="{{$the->xFullStreet}}"
                class="absolute inset-0 h-full w-full object-cover"
              >

              {{-- readable gradient (only for readability) --}}
              <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent"></div>

              {{-- overlay content --}}
              <div class="absolute inset-x-0 bottom-0 p-6 text-white">

                <div class="inline-flex rounded bg-black/60 px-2 py-1 text-xs font-semibold">
                  Featured
                </div>

                <div class="mt-2 text-2xl font-semibold leading-tight drop-shadow">
                  {{$the->xFullStreet}}
                </div>

                <div class="mt-1 text-sm opacity-95 drop-shadow">
                  {{$the->xCity}}, {{$the->xState}} {{$the->xxZip}}
                </div>

                <div class="mt-4 flex items-center gap-3">
                  <img
                    class="h-12 w-12 rounded-full object-cover ring-2 ring-white/90"
                    src='@if($the->theAgent->agtPhoto && $the->theAgent->theAgentCleanup)https://www.realtyrepublic.com/agentPhotos/{{$the->theAgent->theAgentCleanup->newRemID}}/{{$the->theAgent->agtPhoto}}@elseif($the->theAgent->agtPhoto)https://realtyemails.com/HQoffice/{{$the->theOffice->officeID}}/{{$the->theAgent->agtPhoto}}@endif'
                    alt="{{$the->theAgent->agtFullName}}"
                  >

                  <div class="leading-tight">
                    <div class="font-semibold drop-shadow">{{$the->theAgent->agtFullName}}</div>
                    <div class="text-sm opacity-95 drop-shadow">{{$the->theOffice->officeName}}</div>
                    <div class="text-sm opacity-95 drop-shadow">{{$the->theAgent->agtMainPhone}}</div>
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
