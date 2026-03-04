<div id="carouselBackgroundAbsolute"
class="carousel slide"
data-ride="carousel" style="position:relative;z-index:0;">
  <div class="carousel-inner">
    @foreach($newAdds as $the)
      <div class="carousel-item
        @if($loop->iteration===1)
          active
        @endif">
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
                    src='https://www.realtyemails.com/HQoffice/{{$the->theOffice
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
</div>