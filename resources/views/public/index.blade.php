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
            <div class="responsiveImgContainer" style="position:relative;">
              <img class="d-block w-100 responsiveImg"
              src="https://www.realtyrepublic.com/hqphotos/{{$the->theMeta->zipDir}}/{{$the->theMeta
                ->mlsDir}}/{{$the->thePhotos->where('def','=','1')
                ->first()->photoName}}"
                alt="{{$the->xFullStreet}} Main">
            </div>
            <div>
              <div>
                Featured
              </div>
              <div>
                {{$the->xFullStreet}}
              </div>
              <div>
                <div>
                  {{$the->xCity}}, {{$the->xState}} {{$the->xxZip}}
                </div>
              </div>
            </div>
            <div class="carouselAgentWrapper">
              <div>
                <div style="display:table-cell;">
                  @if($the->theAgent->agtPhoto)
                    <div style="display:inline-block;vertical-align:bottom;">
                      @if($the->theAgent->theAgentCleanup)
                        <img class="carouselAgentImg"
                        src='https://www.realtyrepublic.com/agentPhotos/{{$the->theAgent->theAgentCleanup
                          ->newRemID}}/{{$the->theAgent->agtPhoto}}'>
                      @else
                        <img class="carouselAgentImg"
                        src='https://realtyemails.com/HQoffice/{{$the->theOffice
                          ->officeID}}/{{$the->theAgent->agtPhoto}}'>
                      @endif
                    </div>
                  @endif
                  <div style="display:inline-block;vertical-align:bottom;">
                    <div>
                      <div class="carouselAgentName">
                        {{$the->theAgent->agtFullName}}
                      </div>
                      <div class="carouselAgentCompany">
                        {{$the->theOffice->officeName}}
                      </div>
                      <div class="carouselAgentPhone">
                        {{$the->theAgent->agtMainPhone}}
                      </div>
                    </div>
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
