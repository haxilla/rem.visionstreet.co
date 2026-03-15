@if($propInfo->thePhotos->count()==0)
   <div style="padding:15px;margin:15px;">
      <b>{{$propInfo->xFullStreet}}</b>
      <hr>
      NO PHOTOS FOR THIS FLYER!
   </div>
   <?php $photosRequired=1;?>
   @include('flyers.flyerParts.photoRequirement')
@elseif($propInfo->thePhotos->where('def','=','1')->count()==0)
   <div>
      No Default Photo Set!
   </div>
@else

<div style="border-top-left-radius:10px;font-family:arial;
border-top-right-radius:10px;width:100%;line-height:1.52;
background-color:#{{$propInfo->theStyle->flyer_background}};
box-sizing:content-box;color:#333;" class="flyer_background"
data-template="{{$propInfo->theStyle->template}}"
data-flyerbackground="{{$propInfo->theStyle
->flyer_background}}">
   <div>
      <table style="width:100%;">
         <tr style="margin:0;padding:0;width:100%;">
            <td style="width:40%;margin:0;padding:0;">
               <div style="margin-left:20px;"
               class="hlGraphicDiv">
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
            style="color:#{{$propInfo->theStyle->headline_text}};
            width:60%;
            padding:20px;
            padding-right:25px;
            padding-left:0;">
               <div class="headline_text
               xFullStreet clickable"
               style="font-size:12pt;font-weight:bold;">
                  {{$propInfo->xFullStreet}}
               </div>
               @include('flyers.ajaxEdits.xFullStreet')
               <div class="headline_text"
               style="font-size:10pt;">
                  <span class="xCity clickable">
                     {{$propInfo->xCity}},
                  </span>
                  @include('flyers.ajaxEdits.xCity')
                  <span class="xState clickable">
                     {{$propInfo->xState}}
                  </span>
                  @include('flyers.ajaxEdits.xState')
                  <span class="xZip clickable">
                    @if($propInfo->xZip)
                     {{$propInfo->xZip}}
                    @elseif($propInfo->xxZip)
                     {{$propInfo->xxZip}}
                    @endif
                  </span>
               </div>
               <div class="headline_text
               xListPrice clickable"
               style="font-weight:bold;font-size:12pt;">
                  ${{number_format($propInfo->xListPrice)}}
               </div>
               @include('flyers.ajaxEdits.xListPrice')
            </td>
         </tr>
         <tr>
           <td colspan="2" style="padding:0;margin:0;">
             <div style="
             background:#{{$propInfo->theStyle->headline_bar_bg}};
             color:#{{$propInfo->theStyle->headline_bar_text}};
             padding:7px;
             text-align:center;
             font-size:13pt;
             font-weight:bold;
             max-height:65px;
             overflow:hidden;
             @if($propInfo->theStyle->headline_bar_bg=='ffffff'
             || $propInfo->theStyle->headline_bar_bg=='eeeeee'
             || $propInfo->theStyle->headline_bar_bg=='ffffcc')
                border-left:1px solid #ebebeb;
                border-right:1px solid #ebebeb;
             @endif
             position:relative;"
             class="xHeadline clickable
             headline_bar_bg headline_bar_text"
             data-headlinebarbg="{{$propInfo->theStyle->headline_bar_bg}}"
             data-headlinebartext="{{$propInfo->theStyle->headline_bar_text}}">
                <div>
                   {!! $theHeadline!!}
                </div>
             </div>
             @include('flyers.ajaxEdits.xHeadline')
           </td>
         </tr>
      </table>
      @include('flyers.ajaxEdits.headlineGraphic')
      <!-- bottom frame table -->
      <table style="width:100%;border:1px solid #ebebeb;
      border-top:none;padding:0;border-spacing:0;border-collapse:collapse;">
         <tr>
            <td style="padding:0;margin:0;">
               <div style="background-color:#fff;">
                  <div style="padding:7px;padding-bottom:0;">
                     @include('flyers.flyerParts.linksTable_s2pb')
                  </div>
                  <div>
                     @if($totalPhotos==1)
                        <table style="width:100%;text-align:center;padding:0;
                           border-spacing:0;border-collapse:collapse;">
                           <td style="padding:0;margin:0;">
                              <div style="padding:5px;">
                                 <a href="{{URL::route('public.pubShowThePhoto',
                                 ['enc'=>$enc,'photoID'=>$propInfo->thePhotos
                                 ->where('def','=','1')->first()->photoID])}}"
                                 target="_blank">
                                    <img src="{{$fromURL}}/hqphotos/{{$propInfo
                                      ->theMeta->zipDir}}/{{$propInfo
                                      ->theMeta->mlsDir}}/{{$propInfo
                                       ->thePhotos
                                       ->where('resized','=','500')
                                       ->where('def','=','1')
                                       ->first()
                                       ->photoName}}"
                                    class="style2photoLARGE"
                                    @if($propInfo->thePhotos
                                       ->where('def','=','1')
                                       ->first()
                                       ->orient =='wide')
                                       style="width:100%;display:block;"
                                    @else
                                       style="max-width:100%;display:block;"
                                    @endif>
                                 </a>
                              </div>
                           </td>
                        </table>
                     @endif
                     @if($totalPhotos>=2)
                        <table style="width:100%;text-align:center;padding:0;
                        border-spacing:0;border-collapse:collapse;">
                           <tr style="padding:0;margin:0;">
                              <td width="50%" style="padding:0;margin:0;">
                                 <div style="padding:3.5px;padding-left:7px;padding-top:7px;
                                 text-align:center;" class="overlayContainer">
                                    <a href="{{ URL::route('public.pubShowThePhoto',
                                    ['enc'=>$enc,'photoID'=>$propInfo->thePhotos
                                    ->where('resized','=','500')
                                    ->sortBy('ord')
                                    ->first()
                                    ->photoID])}}" target="_blank">
                                       <img
                                       style="@if($propInfo->thePhotos
                                          ->where('resized','=','500')
                                          ->sortBy('ord')
                                          ->first()->orient=='wide')
                                            width:100%;
                                          @else
                                            max-width:100%;
                                          @endif
                                          /* rest of style */
                                          height:200px;display:block;
                                          margin-left:auto;margin-right:auto;"
                                       src="{{$fromURL}}/hqphotos/{{$propInfo
                                        ->theMeta->zipDir}}/{{$propInfo
                                        ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                        ->where('resized','=','500')
                                        ->sortBy('ord')
                                       ->first()
                                       ->photoName}}">
                                    </a>
                                 </div>
                              </td>
                              <td width="50%" style="padding:0;margin:0 auto;">
                                 <div style="padding:3.5px;padding-right:7px;padding-top:7px;
                                 text-align:center;">
                                    @foreach($propInfo->thePhotos
                                      ->where('resized','=','500')
                                       ->sortBy('ord')
                                       ->slice(1)
                                       ->take(1) as $ps)
                                       <a href="{{URL::route('public.pubShowThePhoto',
                                       ['enc'=>$enc,'photoID'=>$ps->photoID])}}"
                                       target="_blank">
                                          <img
                                          style="@if($ps->orient=='wide')
                                            width:100%;
                                          @else
                                            max-width:100%;
                                          @endif
                                            height:200px;display:block;
                                            margin-left:auto;margin-right:auto;"
                                          src="{{$fromURL}}/hqphotos/{{$propInfo
                                            ->theMeta->zipDir}}/{{$propInfo
                                            ->theMeta->mlsDir}}/{{$ps->photoName}}">
                                       </a>
                                    @endforeach
                                 </div>
                              </td>
                           </tr>
                        </table>
                     @endif
                     @if($totalPhotos>=4)
                        <table style="width:100%;text-align:center;padding:0;
                        border-spacing:0;border-collapse:collapse;margin:0;">
                           <tr style="padding:0;margin:0;">
                              <td width="50%" style="padding:0;margin:0;">
                                 <div style="padding:3.5px;padding-left:7px;">
                                    @foreach($propInfo->thePhotos
                                    ->where('resized','=','500')
                                    ->sortBy('ord')
                                    ->slice(2)->take(1) as $ps)
                                       <a href="{{ URL::route('public.pubShowThePhoto',
                                       ['enc'=>$enc,'photoID'=>$ps->photoID])}}">
                                          <img
                                          style="@if($ps->orient=='wide')
                                            width:100%;
                                          @else
                                            max-width:100%;
                                          @endif
                                            height:200px;
                                            display:block;
                                            margin-left:auto;
                                            margin-right:auto;"
                                          src="{{$fromURL}}/hqphotos/{{$propInfo
                                            ->theMeta->zipDir}}/{{$propInfo
                                            ->theMeta->mlsDir}}/{{$ps->photoName}}">
                                       </a>
                                    @endforeach
                                 </div>
                              </td>
                              <td width="50%" style="padding:0;margin:0;">
                                 <div style="padding:3.5px;padding-right:7px;">
                                    @foreach($propInfo->thePhotos
                                    ->where('resized','=','500')
                                    ->sortBy('ord')
                                    ->slice(3)->take(1) as $ps)
                                       <a href="{{URL::route('public.pubShowThePhoto',
                                       ['enc'=>$enc,'photoID'=>$ps->photoID]) }}">
                                          <img
                                          style="@if($ps->orient=='wide')
                                            width:100%;
                                          @else
                                            max-width:100%;
                                          @endif
                                            height:200px;
                                            display:block;
                                            margin-left:auto;
                                            margin-right:auto;"
                                          src="{{$fromURL}}/hqphotos/{{$propInfo
                                            ->theMeta->zipDir}}/{{$propInfo
                                            ->theMeta->mlsDir}}/{{$ps->photoName}}">
                                       </a>
                                    @endforeach
                                 </div>
                              </td>
                           </tr>
                        </table>
                     @endif
                     <!-- property features table -->
                     <table style="width:100%;padding:0;border-spacing:0;
                     border-collapse:collapse;margin:0;font-size:10pt;margin-bottom:3.5px;">
                        <tr>
                           <td style="padding:0;margin:0" colspan="2">
                              @include('flyers.flyerParts.flyerDetails_1col_features')
                           </td>
                        </tr>
                     </table>
                     <!-- end of property features table -->
                     <!-- more photo tables -->
                     @if($totalPhotos>=6)
                        <table style="width:100%;text-align:center;padding:0;
                        border-spacing:0;border-collapse:collapse;margin:0;">
                           <tr>
                              <td width="50%" style="margin:0;padding:0;">
                                 <div style="padding:3.5px;padding-left:7px;">
                                    @foreach($propInfo->thePhotos
                                    ->where('resized','=','500')
                                    ->sortBy('ord')
                                    ->slice(4)->take(1) as $ps)
                                       <a href="{{ URL::route('public.pubShowThePhoto',
                                       ['enc'=>$enc,'photoID'=>$ps->photoID]) }}">
                                          <img src="{{$fromURL}}/hqphotos/{{$propInfo
                                            ->theMeta->zipDir}}/{{$propInfo
                                            ->theMeta->mlsDir}}/{{$ps->photoName}}"
                                          style="@if($ps->orient == 'wide')
                                            width:100%;
                                          @else
                                            max-width:100%;
                                          @endif
                                            height:200px;
                                            display:block;
                                            margin-left:auto;
                                            margin-right:auto;">
                                       </a>
                                    @endforeach
                                 </div>
                              </td>
                              <td width="50%" style="margin:0;padding:0;">
                                 <div style="padding:3.5px;padding-right:7px;">
                                    @foreach($propInfo->thePhotos
                                    ->where('resized','=','500')
                                    ->sortBy('ord')
                                    ->slice(5)->take(1) as $ps)
                                       <a href="{{ URL::route('public.pubShowThePhoto',['enc'=>$enc,
                                       'photoID'=>$ps->photoID])}}">
                                          <img src="{{$fromURL}}/hqphotos/{{$propInfo
                                            ->theMeta->zipDir}}/{{$propInfo
                                            ->theMeta->mlsDir}}/{{$ps->photoName}}"
                                          style="@if($ps->orient == 'wide')
                                            width:100%;
                                          @else
                                            max-width:100%;
                                          @endif
                                            height:200px;
                                            display:block;
                                            margin-left:auto;
                                            margin-right:auto;">
                                       </a>
                                    @endforeach
                                 </div>
                              </td>
                           </tr>
                        </table>
                     @endif
                     @if($totalPhotos>=8)
                        <table style="width:100%;text-align:center;padding:0;
                        border-spacing:0;border-collapse:collapse;margin:0;">
                           <tr>
                              <td width="50%" style="margin:0;padding:0;">
                                 <div style="padding:3.5px;padding-left:7px;">
                                    @foreach($propInfo->thePhotos
                                    ->where('resized','=','500')
                                    ->sortBy('ord')
                                    ->slice(6)->take(1) as $ps)
                                       <a href="{{ URL::route('public.pubShowThePhoto',
                                       ['enc'=>$enc,'photoID'=>$ps->photoID]) }}">
                                          <img src="{{$fromURL}}/hqphotos/{{$propInfo
                                            ->theMeta->zipDir}}/{{$propInfo
                                            ->theMeta->mlsDir}}/{{$ps->photoName}}"
                                          style="@if($ps->orient == 'wide')
                                            width:100%;
                                          @else
                                            max-width:100%;
                                          @endif
                                            height:200px;
                                            display:block;
                                            margin-left:auto;
                                            margin-right:auto;">
                                       </a>
                                    @endforeach
                                 </div>
                              </td>
                              <td width="50%" style="margin:0;padding:0;">
                                 <div style="padding:3.5px;padding-right:7px;">
                                    @foreach($propInfo->thePhotos
                                    ->where('resized','=','500')
                                    ->sortBy('ord')
                                    ->slice(7)->take(1) as $ps)
                                       <a href="{{URL::route('public.pubShowThePhoto',['enc'=>$enc,
                                       'photoID'=>$ps->photoID]) }}">
                                          <img src="{{$fromURL}}/hqphotos/{{$propInfo
                                            ->theMeta->zipDir}}/{{$propInfo
                                            ->theMeta->mlsDir}}/{{$ps->photoName}}"
                                          style="@if($ps->orient == 'wide')
                                            width:100%;
                                          @else
                                            max-width:100%;
                                          @endif
                                            height:200px;
                                            display:block;
                                            margin-left:auto;
                                            margin-right:auto;">
                                       </a>
                                    @endforeach
                                 </div>
                              </td>
                           </tr>
                        </table>
                     @endif
                     <!-- end of photo tables -->
                     <!-- begin agent info table -->
                     <table style="text-align:center;padding:0;
                     border-spacing:0;border-collapse:collapse;margin:0;width:100%;">
                        <tr>
                           <td style="padding:0;margin:0;">
                              <div style="max-width:100%;padding:5px;">
                                 @include('flyers.contactBanner.agtContactBanner')
                              </div>
                           </td>
                        </tr>
                     </table>
                     <!-- end agentinfo table -->
                  </div>
               </div>
            </td>
         </tr>
      </table>
      <!-- end of bottom frame table -->
   </div>
</div>

@endif
