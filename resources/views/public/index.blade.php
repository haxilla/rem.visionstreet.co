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
            <div class="listingCard">

              <img class="bg"
                src="https://realtyrepublic.com/hqphotos/{{$the->theMeta->zipDir}}/{{$the->theMeta->mlsDir}}/{{$the->thePhotos->where('def','=','1')->first()->photoName}}"
                alt="{{$the->xFullStreet}}"
              >

              <div class="shade"></div>

              <div class="overlay">
                <div style="display:inline-block;background:rgba(0,0,0,.6);padding:.25rem .5rem;border-radius:.5rem;font-size:.75rem;font-weight:600;">
                  Featured
                </div>

                <div style="margin-top:.5rem;font-size:1.35rem;font-weight:700;line-height:1.2;">
                  {{$the->xFullStreet}}
                </div>

                <div style="margin-top:.25rem;font-size:.95rem;opacity:.95;">
                  {{$the->xCity}}, {{$the->xState}} {{$the->xxZip}}
                </div>

                <div style="margin-top:1rem;display:flex;align-items:center;gap:.75rem;">
                  <img class="agentImg"
                    src='@if($the->theAgent->agtPhoto && $the->theAgent->theAgentCleanup)https://realtyrepublic.com/agentPhotos/{{$the->theAgent->theAgentCleanup->newRemID}}/{{$the->theAgent->agtPhoto}}@elseif($the->theAgent->agtPhoto)https://realtyemails.com/HQoffice/{{$the->theOffice->officeID}}/{{$the->theAgent->agtPhoto}}@endif'
                    alt="{{$the->theAgent->agtFullName}}"
                  >
                  <div style="line-height:1.15;">
                    <div style="font-weight:700;">{{$the->theAgent->agtFullName}}</div>
                    <div style="font-size:.9rem;opacity:.95;">{{$the->theOffice->officeName}}</div>
                    <div style="font-size:.9rem;opacity:.95;">{{$the->theAgent->agtMainPhone}}</div>
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
