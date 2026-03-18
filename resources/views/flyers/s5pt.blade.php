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
@elseif($propInfo->thePhotos->count()<=1)
  <?php $photosRequired=2; ?>
  @include('flyers.flyerParts.photoRequirement')
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
      <td align="right"
      style="padding:20px;
      padding-right:25px;padding-left:0;
      color:#{{$propInfo->theStyle->headline_text}};
      padding-left:0;
      width:40%;" class="headline_text">
        <div style="font-size:12pt;font-weight:bold;">
           {{ $propInfo->xFullStreet}}
        </div>
        <div style="font-size:10pt;
        font-weight:normal;">
           {{ $propInfo->xCity}}, {{ $propInfo->xState }}
           @if($propInfo->xZip)
              {{ $propInfo->xZip}}
           @else
              {{ $propInfo->xxZip }}
           @endif
        </div>
        <div style="font-size:12pt;font-weight:bold;">
           ${{ number_format($propInfo->xListPrice)}}
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="margin:0;padding:0;">
        <div style="
        background-color:#{{$propInfo->theStyle->headline_bar_bg}};
        color:#{{$propInfo->theStyle->headline_bar_text}};
        padding:7px;
        text-align:center;
        font-weight:bold;
        font-size:13pt;
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
   <table style="width:100%;border:1px solid #eee;border-top:none;
   padding:0;border-spacing:0;border-collapse:collapse;
   background-color:#fff;">
      <tr>
         <td style="padding:0;margin:0;">
            <div style="padding:7px;padding-bottom:0;">
               @include('flyers.flyerParts.linksTable_s2pb')
            </div>
         </td>
      </tr>
      <tr>
         <td>
            <table style="padding:0;border-spacing:0;
            border-collapse:collapse;width:100%;
            background-color:#fff;box-sizing:content-box;">
               <tr>
                  <td style="width:75%;
                  margin:0;
                  padding:0;
                  vertical-align:top;">
                     <div style="padding:7px;
                     padding-right:10px;">
                        <a href="#" target="_blank">
                           <img
                           src="{{$fromURL}}/hqphotos/{{$propInfo
                            ->theMeta->zipDir}}/{{$propInfo
                            ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                            ->where('def','=','1')
                            ->where('resized','=','500')
                            ->first()
                            ->photoName}}"
                           class="style5DefPhotoImg"
                           style="
                           @if($propInfo->thePhotos
                            ->where('def','=','1')
                            ->where('resized','=','500')
                            ->first()
                            ->orient=='wide')
                            width:100%;
                           @else
                            max-width:100%;
                           @endif
                           height:300px;
                           display:block;"
                           id="style5LargePhoto">
                        </a>
                     </div>
                  </td>
                  <td style="width:25%;
                     margin:0;
                     padding:0;
                     vertical-align:top;">
                        <div style="padding:10px;font-size:9pt;
                        text-align:center;padding-bottom:5px;">
                           Click any thumbnail to see larger version
                        </div>
                        <div style="border:1px solid #ebebeb;
                           margin-right:10px;padding:2.5px;">
                           <!-- height 255px works if needed -->
                           <table style="padding:0;border-spacing:0;
                           border-collapse:collapse;width:100%;">
                              <tr>
                                 <td width="50%" style="margin:0;padding:0;">
                                    <div style="padding:2.5px;">
                                       @if($propInfo->thePhotos->sortBy('ord')->take(1)->first())
                                          @if($display==='email')
                                             <a href="#" target="_blank">
                                                <img
                                                src="{{$fromURL1}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                  ->where('resized','=','500')
                                                   ->sortBy('ord')
                                                   ->take(1)
                                                   ->first()->photoName}}"
                                                class="style5ThumbImg"
                                                style="width:100%;
                                                height:45px;display:block;">
                                             </a>
                                          @else
                                             <a href="#">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                  ->where('resized','=','500')
                                                   ->sortBy('ord')
                                                   ->take(1)
                                                   ->first()
                                                   ->photoName}}"
                                                class="style5ThumbImg">
                                             </a>
                                          @endif
                                       @endif
                                    </div>
                                 </td>
                                 <td width="50%" style="margin:0;padding:0;">
                                    @if($propInfo->thePhotos
                                    ->slice(1)->take(1))
                                       <div style="padding:2.5px;">
                                          @if($display==='email')
                                             <a href="#">
                                                  <img
                                                  src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                  ->where('resized','=','500')
                                                  ->sortBy('ord')
                                                  ->slice(1)->take(1)
                                                  ->first()
                                                  ->photoName}}"
                                                  style="width:100%;
                                                  height:45px;margin:auto;
                                                  display:block;">
                                             </a>
                                          @else
                                             <a href="#">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                  ->where('resized','=','500')
                                                   ->sortBy('ord')
                                                   ->slice(1)->take(1)
                                                   ->first()
                                                   ->photoName}}"
                                                class="style5ThumbImg">
                                             </a>
                                          @endif
                                       </div>
                                    @endif
                                 </td>
                              </tr>
                              <tr>
                                 <td width="50%" style="margin:0;padding:0;">
                                    @if($propInfo->thePhotos->slice(2)
                                    ->take(1)->first())
                                       <div style="padding:2.5px;">
                                          @if($display==='email')
                                             <a href="#" target="_blank">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                    ->where('resized','=','500')
                                                   ->sortBy('ord')
                                                   ->slice(2)->take(1)
                                                   ->first()
                                                   ->photoName}}"
                                                class="style5ThumbImg"
                                                style="width:100%;height:45px;
                                                display:block;">
                                             </a>
                                          @else
                                             <a href="#">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                    ->where('resized','=','500')
                                                   ->sortBy('ord')
                                                   ->slice(2)->take(1)
                                                   ->first()->photoName}}"
                                                class="style5ThumbImg">
                                             </a>
                                          @endif
                                       </div>
                                    @endif
                                 </td>
                                 <td width="50%" style="margin:0;padding:0;">
                                    @if($propInfo->thePhotos
                                    ->sortBy('ord')
                                    ->slice(3)->take(1)->first())
                                       <div style="padding:2.5px;">
                                          @if($display==='email')
                                             <a href="#" target="_blank">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                    ->where('resized','=','500')
                                                   ->sortBy('ord')
                                                   ->slice(3)->take(1)
                                                   ->first()
                                                   ->photoName}}"
                                                class="style5ThumbImg"
                                                style="width:100%;height:45px;
                                                display:block;">
                                             </a>
                                          @else
                                             <a href="#">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                    ->where('resized','=','500')
                                                   ->sortBy('ord')
                                                   ->slice(3)->take(1)
                                                   ->first()
                                                   ->photoName}}"
                                                class="style5ThumbImg">
                                             </a>
                                          @endif
                                       </div>
                                    @endif
                                 </td>
                              </tr>
                              <tr>
                                 <td width="50%" style="margin:0;padding:0;">
                                    @if($propInfo->thePhotos->slice(4)
                                    ->take(1)->first())
                                       <div style="padding:2.5px;">
                                          @if($display==='email')
                                             <a href="#" target="_blank">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                    ->where('resized','=','500')
                                                   ->sortBy('ord')
                                                   ->slice(4)->take(1)
                                                   ->first()
                                                   ->photoName}}"
                                                class="style5ThumbImg"
                                                style="width:100%;height:45px;
                                                display:block;">
                                             </a>
                                          @else
                                             <a href="#">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                    ->where('resized','=','500')
                                                   ->sortBy('ord')
                                                   ->slice(4)->take(1)
                                                   ->first()
                                                   ->photoName}}"
                                                class="style5ThumbImg">
                                             </a>
                                          @endif
                                       </div>
                                    @endif
                                 </td>
                                 <td width="50%" style="margin:0;padding:0;">
                                    @if($propInfo->thePhotos->slice(5)
                                    ->take(1)->first())
                                       <div style="padding:2.5px;">
                                          @if($display==='email')
                                             <a href="#" target="_blank">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                ->theMeta->zipDir}}/{{$propInfo
                                                ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                  ->where('resized','=','500')
                                                ->sortBy('ord')
                                                ->slice(5)->take(1)
                                                ->first()->photoName}}"
                                                class="style5ThumbImg"
                                                style="width:100%;height:45px;
                                                display:block;">
                                             </a>
                                          @else
                                             <a href="#">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                ->theMeta->zipDir}}/{{$propInfo
                                                ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                  ->where('resized','=','500')
                                                ->sortBy('ord')
                                                ->slice(5)->take(1)
                                                ->first()->photoName}}"
                                                class="style5ThumbImg">
                                             </a>
                                          @endif
                                       </div>
                                    @endif
                                 </td>
                              </tr>
                              <tr>
                                 <td width="50%" style="margin:0;padding:0;">
                                    @if($propInfo->thePhotos->slice(6)
                                    ->take(1)->first())
                                       <div style="padding:2.5px;">
                                          @if($display==='email')
                                             <a href="#" target="_blank">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                    ->where('resized','=','500')
                                                   ->sortBy('ord')
                                                   ->slice(6)->take(1)
                                                   ->first()
                                                   ->photoName}}"
                                                class="style5ThumbImg"
                                                style="width:100%;height:45px;
                                                display:block;">
                                             </a>
                                          @else
                                             <a href="#">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                    ->where('resized','=','500')
                                                   ->sortBy('ord')
                                                   ->slice(6)->take(1)
                                                   ->first()->photoName}}"
                                                class="style5ThumbImg">
                                             </a>
                                          @endif
                                       </div>
                                    @endif
                                 </td>
                                 <td width="50%" style="margin:0;padding:0;">
                                    @if($propInfo->thePhotos->slice(7)->take(1)
                                    ->first())
                                       <div style="padding:2.5px;">
                                          @if($display==='email')
                                             <a href="#" target="_blank">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                    ->where('resized','=','500')
                                                   ->sortBy('ord')
                                                   ->slice(7)->take(1)
                                                   ->first()->photoName}}"
                                                class="style5ThumbImg"
                                                style="width:100%;height:45px;
                                                display:block;">
                                             </a>
                                          @else
                                             <a href="#">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                    ->where('resized','=','500')
                                                   ->sortBy('ord')
                                                   ->slice(7)->take(1)
                                                   ->first()
                                                   ->photoName}}"
                                                class="style5ThumbImg">
                                             </a>
                                          @endif
                                       </div>
                                    @endif
                                 </td>
                              </tr>
                              <tr>
                                 <td width="50%" style="margin:0;padding:0;">
                                    @if($propInfo->thePhotos->slice(8)
                                    ->take(1)->first())
                                       <div style="padding:2.5px;">
                                          @if($display==='email')
                                             <a href="#" target="_blank">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                    ->where('resized','=','500')
                                                   ->sortby('ord')
                                                   ->slice(8)->take(1)
                                                   ->first()->photoName}}"
                                                class="style5ThumbImg"
                                                style="width:100%;height:45px;
                                                display:block;">
                                             </a>
                                          @else
                                             <a href="#">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                    ->where('resized','=','500')
                                                   ->sortby('ord')
                                                   ->slice(8)->take(1)
                                                   ->first()->photoName}}"
                                                class="style5ThumbImg">
                                             </a>
                                          @endif
                                       </div>
                                    @endif
                                 </td>
                                 <td width="50%" style="margin:0;padding:0;">
                                    @if($propInfo->thePhotos->slice(9)
                                    ->take(1)->first())
                                       <div style="padding:2.5px;">
                                          @if($display==='email')
                                             <a href="#" target="_blank">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$zipDir}}/{{$mlsDir}}/{{$propInfo->thePhotos
                                                  ->where('resized','=','500')
                                                   ->sortBy('ord')
                                                   ->slice(9)->take(1)
                                                   ->first()->photoName}}"
                                                class="style5ThumbImg"
                                                style="width:100%;height:45px;
                                                display:block;">
                                             </a>
                                          @else
                                             <a href="#">
                                                <img
                                                src="{{$fromURL}}/hqphotos/{{$propInfo
                                                  ->theMeta->zipDir}}/{{$propInfo
                                                  ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                                                    ->where('resized','=','500')
                                                   ->sortBy('ord')
                                                   ->slice(9)->take(1)
                                                   ->first()->photoName}}"
                                                class="style5ThumbImg">
                                             </a>
                                          @endif
                                       </div>
                                    @endif
                                 </td>
                              </tr>
                           </table>
                        </div>
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
         <tr>
            <td>
               @include('flyers.flyerParts.flyerDetails_1col_features')
            </td>
         </tr>
         <tr>
            <td>
               <div style="padding:10px;">
                  @include('flyers.flyerParts.agtContactBanner')
               </div>
            </td>
         </tr>
      </table>
   </div>

@endif
