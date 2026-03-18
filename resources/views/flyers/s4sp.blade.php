@if($propInfo->thePhotos->count()==0)
   <div style="padding:15px;margin:15px;">
      <div style="padding:15px;margin:15px;">
         <b>{{$propInfo->xFullStreet}}</b>
         <hr>
         NO PHOTOS FOR THIS FLYER!
      </div>
   </div>
   <?php $photosRequired=1;?>
   @include('flyers.flyerParts.photoRequirement')
@elseif($propInfo->thePhotos->where('def','=','1')->count()==0)
   <div>
      No Default Photo Set!
   </div>
@else

<div style="border-top-left-radius:10px;
border-top-right-radius:10px;
background-color:#{{$propInfo->theStyle->flyer_background}};
color:#333;font-family:arial;
margin-bottom:15px;line-height:1.52"
class="flyer_background"
data-template="{{$propInfo->theStyle->template}}"
data-flyerbackground="{{$propInfo->theStyle
->flyer_background}}">
   <!-- header table -->
   <table style="width:100%;">
      <tr>
        <td style="width:40%;padding:0;">
          <div style="margin-left:20px;">
             <img src="{{$fromURL}}/images/headline_graphics/{{$propInfo
              ->theStyle->graphic_words}}/{{$propInfo
              ->theStyle->graphic_style}}/{{$hlGraphic}}"
             class="hlGraphic"
             data-fromURL="{{$fromURL}}"
             data-graphicwords="{{$propInfo->theStyle->graphic_words}}"
             data-graphicstyle="{{$propInfo->theStyle->graphic_style}}"
             data-graphictextcolor="{{$propInfo->theStyle->graphic_textcolor}}">
          </div>
        </td>
        <td align="right"
        style="padding:20px;
        padding-right:25px;
        color:#{{$propInfo->theStyle->headline_text}};
        padding-left:0;
        width:60%;">
          <div class="headline_text"
          style="font-size:12pt;font-weight:bold;">
           {{$propInfo->xFullStreet}}
          </div>
          <div class="headline_text"
          style="font-size:10pt;font-weight:normal;">
           {{ $propInfo->xCity}}, {{ $propInfo->xState }}
           @if($propInfo->xZip)
            {{$propInfo->xZip}}
           @elseif($propInfo->xxZip)
            {{$propInfo->xxZip}}
           @endif
          </div>
          <div class="headline_text"
          style="font-size:12pt;
          font-weight:bold;">
            ${{ number_format($propInfo->xListPrice)}}
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2"
        style="padding:0;margin:0;">
          <div style="
          @if($propInfo->theStyle
          ->headline_bar_bg=='ffffff'
          ||$propInfo->theStyle
          ->headline_bar_bg=='ffffcc')
            border-left:1px solid #eee;
            border-right:1px solid #eee;
          @endif
          background-color:#{{$propInfo->theStyle->headline_bar_bg}};
          color:#{{$propInfo->theStyle->headline_bar_text}};
          padding:7px;
          text-align:center;
          font-weight:bold;
          font-size:13pt;
          max-height:65px;
          overflow:hidden;"
          class="headline_bar_bg
          headline_bar_text"
          data-headlinebarbg="{{$propInfo
          ->theStyle->headline_bar_bg}}"
          data-headlinebartext="{{$propInfo
          ->theStyle->headlinebartext}}">
            {!! $theHeadline !!}
          </div>
        </td>
      </tr>
   </table>
   <!-- end header table -->
   <!-- bottom frame table -->
   <table style="width:100%;border:1px solid #eee;border-top:none;
   padding:0;border-spacing:0;border-collapse:collapse;
   background-color:#fff;">
      <tr style="background-color:#fff;">
         <td style="padding:0;
            vertical-align:top;
            width:35%;">
            <div>
               @include('flyers.flyerParts.tallboxDetails')
            </div>
         </td>
         <td style="width:70%;padding:0;margin:0;
         vertical-align:top;">
            <div style="padding-top:7px;padding-right:7px;">
                <a href="#" target="_blank">
                  <img src="{{$fromURL}}/hqphotos/{{$propInfo
                  ->theMeta->zipDir}}/{{$propInfo
                  ->theMeta->mlsDir}}/{{$propInfo
                  ->thePhotos
                  ->where('resized','=','500')
                  ->where('def','=','1')
                  ->first()->photoName}}"
                  id="largePhoto"
                  style="@if($propInfo->thePhotos()
                  ->where('def','=','1')
                  ->first()->orient=='wide')
                    width:100%;
                  @else
                    max-width:100%;
                  @endif
                    display:block;
                    margin-left:auto;
                    margin-right:auto;
                    border-radius:5px;
                    height:265px;">
                </a>
            </div>
         </td>
      </tr>
      <tr style="background-color:#fff;">
         <td colspan="2">
            <div style="margin:5px;">
               <table style="width:100%;padding:0;border-spacing:0;
               border-collapse:collapse;margin:0;">
                  <tr>
                     <td colspan="2">
                        <div style="border:1px solid #eee;
                        padding-top:5px;
                        background-color:#f9f9f9;
                        border-radius:5px;">
                           @include('flyers.flyerParts.linksTable_hasMls')
                           @include('flyers.flyerParts.flyerDetails_noBullets_noMls')
                        </div>
                     </td>
                  </tr>
               </table>
            </div>
         </td>
      </tr>
      <tr style="background-color:#ebebeb;text-align:center;">
         <td colspan="2">
            <table style="width:100%;padding:0;border-spacing:0;border-collapse:collapse;
               margin:0;">
               <tr>
                  <td style="width:33%;margin:0;padding:0;">
                     <div style="padding:5px;">
                        @foreach($propInfo->thePhotos
                        ->where('resized','=','500')
                        ->sortByDesc('def')
                        ->sortBy('ord')
                        ->slice(1)->take(1) as $ps)
                           <a href="#" target="_blank">
                              <img src="{{$fromURL}}/hqphotos/{{$propInfo
                                ->theMeta->zipDir}}/{{$propInfo
                                ->theMeta->mlsDir}}/{{$ps->photoName}}"
                              style="@if($ps->orient=='wide')
                                width:100%;
                              @else
                                max-width:100%;
                              @endif
                                display:block;
                                height:130px;">
                           </a>
                        @endforeach
                     </div>
                  </td>
                  <td style="width:33%;margin:0;padding:0;">
                     <div style="padding:5px;">
                        @foreach($propInfo->thePhotos
                        ->where('resized','=','500')
                        ->sortByDesc('def')
                        ->sortBy('ord')
                        ->slice(2)->take(1) as $ps)
                           <a href="#" target="_blank">
                              <img src="{{$fromURL}}/hqphotos/{{$propInfo
                              ->theMeta->zipDir}}/{{$propInfo
                              ->theMeta->mlsDir}}/{{$ps->photoName}}"
                              style="@if($ps->orient=='wide')
                                width:100%;
                              @else
                                max-width:100%;
                              @endif
                                display:block;
                                height:130px;">
                           </a>
                        @endforeach
                     </div>
                  </td>
                  <td style="width:33%;padding:0;">
                     <div style="padding:5px;">
                        @foreach($propInfo->thePhotos
                        ->where('resized','=','500')
                        ->sortByDesc('def')
                        ->sortBy('ord')
                        ->slice(3)->take(1) as $ps)
                           <a href="#" target="_blank">
                              <img src="{{$fromURL}}/hqphotos/{{$propInfo
                              ->theMeta->zipDir}}/{{$propInfo
                              ->theMeta->mlsDir}}/{{$ps->photoName}}"
                              style="@if($ps->orient=='wide')
                                width:100%;
                              @else
                                max-width:100%;
                              @endif
                                display:block;
                                height:130px;">
                           </a>
                        @endforeach
                     </div>
                  </td>
               </tr>
            </table>
         </td>
      </tr>
      <tr style="background-color:#fff;">
         <td colspan="2">

         </td>
      </tr>
      <tr style="background-color:#fff;">
         <td colspan="2" style="margin:0;padding:0;">
            <div style="max-width:100%;padding:10px;">
               @include('flyers.flyerParts.agtContactBanner')
            </div>
         </td>
      </tr>
   </table>
</div>

@endif
