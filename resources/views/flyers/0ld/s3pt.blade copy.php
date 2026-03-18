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
margin-bottom:15px;line-height:1.52;overflow:hidden;"
class="flyer_background"
data-template="{{$propInfo->theStyle->template}}"
data-flyerbackground="{{$propInfo->theStyle
->flyer_background}}">
   <!-- header table -->
   <table style="width:100%;"
   cellspacing="0" cellpadding="0">
      <tr>
        <td style="margin:0;padding:0;width:40%;">
          <div style="margin-left:20px;">
             <img
             src="{{$fromURL}}/images/headline_graphics/{{$propInfo
              ->theStyle->graphic_words}}/{{$propInfo
              ->theStyle->graphic_style}}/{{$hlGraphic}}"
             class="hlGraphic"
             data-fromURL="{{$fromURL}}"
             data-graphicwords="{{$propInfo->theStyle->graphic_words}}"
             data-graphicstyle="{{$propInfo->theStyle->graphic_style}}"
             data-graphictextcolor="{{$propInfo->theStyle->graphic_textcolor}}">
          </div>
        </td>
        <td style="padding:20px;
        padding-right:25px;
        color:#{{$propInfo->theStyle->headline_text}};
        padding-left:0;
        width:40%;
        text-align:right;">
            <div class="headline_text"
            data-headlinetext="{{$propInfo->theStyle->headline_text}}"
            style="font-weight:bold;font-size:12pt;">
               {{ $propInfo->xFullStreet}}
            </div>
            <div class="headline_text"
            style="font-weight:normal;font-size:10pt;">
               {{$propInfo->xCity}}, {{$propInfo->xState}}
               @if($propInfo->xZip)
                {{$propInfo->xZip}}
               @elseif($propInfo->xxZip)
                {{$propInfo->xxZip}}
               @endif
            </div>
            <div class="headline_text"
            style="font-size:12pt;font-weight:bold;">
               ${{number_format($propInfo->xListPrice)}}
            </div>
         </td>
      </tr>
      <tr>
        <td colspan="2">
          <div style="background-color:#{{$propInfo->theStyle->headline_bar_bg}};
          color:#{{$propInfo->theStyle->headline_bar_text}};
          text-align:center;
          font-size:13pt;
          padding:7px;
          font-weight:bold;
          max-height:65px;
          overflow:hidden;"
          class="mdbxHeadline headline_bar_bg headline_bar_text"
          data-headlinebarbg="{{$propInfo->theStyle->headline_bar_bg}}"
          data-headlinebartext="{{$propInfo->theStyle->headline_bar_text}}">
             {!! $theHeadline !!}
          </div>
        </td>
      </tr>
   </table>
   <!-- end header table -->
   <!-- bottom frame table -->
   <table class="flyerContent"
   style="width:100%;border:1px solid #eee;border-top:none;
   padding:0;border-spacing:0;border-collapse:collapse;">
      <tr>
         <td style="padding:0;margin:0;">
            <div style="background-color:#fff;">
               <div>
                  <!--photo / features table -->
                  <table style="padding:0;border-spacing:0;border-collapse:collapse;
                  width:100%;">
                     <tr>
                        <td style="width:70%;padding:0;margin:0;
                        vertical-align:top;">
                           <div style="padding:7px;padding-right:0;">
                             <a href="{{URL::route('public.pubShowThePhoto',['enc'=>$enc,
                             'photoID'=>$propInfo->thePhotos->where('def','=','1')
                             ->where('resized','=','500')
                             ->first()->photoID])}}">
                                <img src="{{$fromURL}}/hqphotos/{{$propInfo
                                ->theMeta->zipDir}}/{{$propInfo
                                ->theMeta->mlsDir}}/{{$propInfo
                                ->thePhotos
                                ->where('resized','=','500')
                                ->where('def','=','1')
                                ->first()
                                ->photoName}}"
                                id="largePhoto"
                                style="@if($propInfo->thePhotos()
                                ->where('def','=','1')
                                ->where('resized','=','500')
                                ->first()->orient=='wide')
                                  width:100%;
                                @else
                                  max-width:100%;
                                  margin-left:auto;
                                  margin-right:auto;
                                @endif
                                  display:block;
                                  border-radius:5px;
                                  height:267px;">
                             </a>
                           </div>
                        </td>
                        <td style="width:30%;padding:0;margin:0;
                        vertical-align:top;">
                           @include('flyers.flyerParts.tallboxDetails')
                        </td>
                     </tr>
                     <tr>
                        <td colspan="2" style="padding:0;margin:0;">
                           <div style="padding-left:7px;
                           box-sizing:content-box;">
                              @if($propInfo->thePhotos
                              ->where('resized','=','500')
                              ->count()>1)
                                @foreach($propInfo->thePhotos
                                ->where('resized','=','500')
                                ->sortBy('ord')
                                ->take(7) as $the)
                                   <div style="padding-right:1.5px;
                                   box-sizing:content-box;
                                   display:inline-block;">
                                      <a href="@if($display=='email')
                                        {{URL::route('public.pubShowThePhoto',
                                        ['enc'=>$enc,'photoID'=>$the->photoID,])}}
                                      @else#@endif">
                                         <img src="{{$fromURL}}/hqphotos/{{$propInfo
                                          ->theMeta->zipDir}}/{{$propInfo
                                          ->theMeta->mlsDir}}/{{$the->photoName}}"
                                         style="max-width:78px;
                                         height:52px;
                                         display:block;
                                         margin-left:auto;
                                         margin-right:auto;
                                         border-radius:3px;">
                                      </a>
                                   </div>
                                 @endforeach
                              @endif
                           </div>
                        </td>
                     </tr>
                     <tr>
                        <!-- ALL CONTENT FOR BOTTOM OF ONE COLUMN FLYER -->
                        <td colspan="2" style="padding:0;margin:0;">
                           @include('flyers.flyerParts.linksTable_hasMls')
                           @include('flyers.flyerParts.flyerDetails_noBullets_noMls')
                        </td>
                     </tr>
                     <tr>
                        <td colspan="2" style="margin:0;padding:0;">
                           <div style="max-width:100%;border-top:1px solid #eee;">
                              @include('flyers.contactBanner.agtContactBanner')
                           </div>
                        </td>
                     </tr>
                  </table>
                  <!-- end of photo / features table -->
               </div>
            </div>
         </td>
      </tr>
   </table>
</div>

@endif
