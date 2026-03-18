@if($propInfo->thePhotos->count()==0)
  <div style="padding:15px;margin:15px;">
    <b>{{$propInfo->xFullStreet}}</b>
    <hr>
    NO PHOTOS FOR THIS FLYER!
  </div>
  <?php $photosRequired=4;?>
  @include('flyers.flyerParts.photoRequirement')
@elseif($propInfo->thePhotos->where('def','=','1')->count()==0)
  <div>
    No Default Photo Set!
  </div>
@elseif($propInfo->thePhotos->count()<=3)
  <?php $photosRequired=4; ?>
  @include('flyers.flyerParts.photoRequirement')
@else
<div style="border-top-left-radius:10px;font-family:arial;
border-top-right-radius:10px;width:100%;line-height:1.52;
background-color:#{{$propInfo->theStyle->flyer_background}};
box-sizing:content-box;" class="flyer_background"
data-template="{{$propInfo->theStyle->template}}"
data-flyerbackground="{{$propInfo
->theStyle->flyer_background}}">
  <!--- header table -->
  <table style="width:100%;table-layout:fixed;
  box-sizing:content-box;border-collapse:collapse;">
    <tr>
      <td style="width:40%;padding-right:25px;">
        <div style="width:100%;margin:20px;margin-bottom:10px;">
            <img src="{{$fromURL}}/images/headline_graphics/{{$propInfo
              ->theStyle->graphic_words}}/{{$propInfo
              ->theStyle->graphic_style}}/{{$hlGraphic}}"
            style="max-width:100%;"
            class="hlGraphic"
            data-fromURL="{{$fromURL}}"
            data-graphicwords="{{$propInfo->theStyle->graphic_words}}"
            data-graphicstyle="{{$propInfo->theStyle->graphic_style}}"
            data-graphictextcolor="{{$propInfo->theStyle->graphic_textcolor}}">
        </div>
      </td >
      <td style="width:40%;margin-right:55px;">
        <div style="width:100%;
        text-align:center;
        font-size:12pt;
        font-weight:bold;
        color:#{{$propInfo->theStyle->headline_text}};
        padding-top:15px;padding-bottom:15px;">
            <div class="headline_text"
            style="padding-left:25px;
            max-height:70px;
            overflow:hidden;"
            data-headlinetext="{{$propInfo->theStyle->headline_text}}">
              {!! $theHeadline !!}
            </div>
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="margin:0;padding:0;">
        <div>
          <img src="{{$fromURL}}/images/double_line_tran.gif"
          style="width:100%;display:block;">
        </div>
      </td>
    </tr>
  </table>
  <!-- end of header table -->
  <!-- bottom frame table -->
  <table style="border-collapse:collapse;">
    <tr style="margin:0;padding:0;">
      <td style="padding-left:5px;
        vertical-align:top;
        width:55%;
        box-sizing:content-box;">
        <div
          @if($display=='screen' && $totalPhotos>7)
            class="style1LeftBackground"
          @endif
          @if($display=='email' && $totalPhotos > 7)
            style="background-color:#ffffff;
            height:1269px;
            color:#333;
            text-align:center;
            box-sizing:content-box;
            width:100%;"
          @elseif($display=='screen' && $totalPhotos==7)
            class="style1LeftBackground7photos"
          @elseif($display=='email' && $totalPhotos==7)
            style="background-color:#ffffff;
            height:1087px;
            color:#333;
            text-align:center;
            box-sizing:content-box;
            width:100%;"
          @elseif($display=='screen' && $totalPhotos==6)
            class="style1LeftBackground6photos"
          @elseif($display=='email' && $totalPhotos==6)
            style="background-color:#ffffff;
            height:905px;
            color:#333;
            text-align:center;
            box-sizing:content-box;
            width:100%;"
          @elseif($display=='screen' && $totalPhotos==5)
            class="style1LeftBackground5photos"
          @elseif($display=='email' && $totalPhotos==5)
            style="background-color:#ffffff;
            height:723px;
            color:#333;
            text-align:center;
            box-sizing:content-box;
            width:100%;"
          @elseif($display=='screen' && $totalPhotos==4)
            class="style1LeftBackground4photos"
          @elseif($display=='email' && $totalPhotos==4)
            style="background-color:#ffffff;
            height:541px;
            color:#333;
            text-align:center;
            box-sizing:content-box;
            width:100%;"
          @endif>
          <div style="padding:5px;box-sizing:content-box;">
            <a href="#"
            target="_blank">
              <img src="{{$fromURL}}/hqphotos/{{$propInfo
                ->theMeta->zipDir}}/{{$propInfo
                ->theMeta->mlsDir}}/{{$propInfo->thePhotos
                ->where('def','=','1')
                ->where('resized','=','500')
                ->first()->photoName}}"
              @if($display=='screen')
                @if($propInfo->thePhotos
                  ->where('def','=','1')
                  ->where('resized','=','500')
                  ->first()->orient=='wide')
                  class="style1Default"
                @else
                  class="style1DefaultTall"
                @endif
              @endif
              @if($display=='email')
                style="max-width:100%;
                height:215px;
                display:block;
                margin-left:auto;
                margin-right:auto;"
              @endif>
            </a>
          </div>
          <div
            @if($display=='screen' && $totalPhotos > 7)
              class="style1RemarksFrame"
            @elseif($display=='screen' && $totalPhotos==7)
              class="style1RemarksFrame7photo"
            @elseif($display=='screen' && $totalPhotos==6)
              class="style1RemarksFrame6photo"
            @elseif($display=='screen' && $totalPhotos==5)
              class="style1RemarksFrame5photo"
            @elseif($display=='screen' && $totalPhotos==4)
              class="style1RemarksFrame4photo"
            @endif
            @if($display=='email')
              style="padding:10px;box-sizing:content-box;"
            @endif>
              @if($totalPhotos<='5')
              <div style="text-align:center;">
                @include('flyers.flyerParts.style1shortsummary')
              </div>
              @endif
              <div>
                @include('flyers.flyerParts.style1Address')
                <div style="margin-top:10px;">
                  <img src="{{$fromURL}}/images/trimmedNewScroll4.png"
                  style="display:block;">
                </div>
                <div
                @if($display=='screen' && $totalPhotos > 7)
                class="style1RoundedRemarks"
                @elseif($display=='screen' && $totalPhotos==7)
                class="style1RoundedRemarks7photo"
                @elseif($display=='screen' && $totalPhotos==6)
                class="style1RoundedRemarks6photo"
                @elseif($display=='screen' && $totalPhotos==5)
                class="style1RoundedRemarks5photo"
                @elseif($display=='screen' && $totalPhotos==4)
                class="style1RoundedRemarks4photo"
                @elseif($display=='email' && $totalPhotos>7)
                  style="margin-bottom:15px;background-color:#f9f9f9;
                  border-bottom-left-radius:10px;border-bottom-right-radius:10px;
                  padding:15px;padding-top:0;height:565px;box-sizing:content-box;"
                @elseif($display=='email' && $totalPhotos==7)
                  style="border-radius:10px;height:380px;box-sizing:content-box;
                  margin-bottom:15px;background-color:#f9f9f9;border-bottom-left-radius:10px;
                  border-bottom-right-radius:10px;padding:15px;padding-top:0;"
                @elseif($display=='email' && $totalPhotos==6)
                  style="padding:15px;text-align:left;line-height:1.75 !important;
                  background-color:#f9f9f9;font-size:11pt !important;padding-top:5px;
                  border-bottom-left-radius:10px;border-bottom-right-radius:10px;
                  overflow:hidden;height:190px;font-size:12pt;box-sizing:content-box;"
                @elseif($display=='email' && $totalPhotos==5)
                  style="margin-bottom:15px;background-color:#f9f9f9;
                  border-bottom-left-radius:10px;border-bottom-right-radius:10px;
                  padding:15px;padding-top:0;height:260px;box-sizing:content-box;"
                @elseif($display=='email' && $totalPhotos==4)
                  style="margin-bottom:15px;background-color:#f9f9f9;
                  border-bottom-left-radius:10px;border-bottom-right-radius:10px;
                  padding:15px;padding-top:0;border-radius:10px;height:100px;
                  margin-top:0;box-sizing:content-box;"
                @endif>
                  @include('flyers.flyerParts.style1MLSnum')
                  @include('flyers.flyerParts.style1PubRemarks')
                  @include('flyers.flyerParts.style1moreInfo')
                </div>
                @include('flyers.flyerParts.style1FlyerLinks')
                @if($totalPhotos>5)
                  <div style="font-size:11pt;margin-top:15px;margin-bottom:10px;
                  padding-left:10px;text-align:left;">
                    <div style="font-weight:bold;">
                      <u>Major Cross Streets</u>
                    </div>
                    <div>
                      {{$propInfo->theMap->xIntersection}}
                    </div>
                  </div>
                  @include('flyers.flyerParts.bulletPoints1')
                @endif
              </div>
          </div>
        </td>
        <td class="style1AllPhotosTD"
        @if($display=='email')
          style="padding-left:5px;
          padding-right:5px;
          margin:0;
          vertical-align:top;
          width:45%"
        @endif>
        <div>
          @foreach($propInfo->thePhotos
            ->where('def','!=','1')
            ->where('resized','=','500')
            ->sortBy('ord')
            ->take(7) as $the)
            <div class="style1PhotoFrame"
              @if($display=='email')
                style="padding:5px;
                background-color:#fff;
                margin-bottom:5px;"
              @endif>
              <a href="#" target="_blank">
                <img
                src="{{$fromURL}}/hqphotos/{{$propInfo
                  ->theMeta->zipDir}}/{{$propInfo
                  ->theMeta->mlsDir}}/{{$the->photoName}}"
                @if($display=='screen')
                  @if($the->orient=='wide')
                    class="style1Photos"
                  @else
                    class="stylePhotosTall"
                  @endif
                @endif
                @if($display=='email')
                  @if($the->orient=="wide")
                    style="display:block;
                    height:167px;
                    width:100%;"
                  @else
                    style="display:block;
                    height:167px;
                    margin-left:auto;
                    margin-right:auto;
                    max-width:100%;"
                  @endif

                @endif>
              </a>
            </div>
          @endforeach
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="padding:0;margin:0;">
        <div style="padding:5px;padding-top:0">
          <!-- begin agent info table -->
          <table style="text-align:center;padding:0;margin:0;
          border-spacing:0;border-collapse:collapse;width:100%;">
            <tr>
               <td align="center" style="padding:0;margin:0;">
                  <div>
                     @include('flyers.flyerParts.agtContactBanner')
                  </div>
               </td>
            </tr>
          </table>
          <!-- end agentinfo table -->
        </div>
      </td>
    </tr>
  </table>
  <!-- end of bottom frame table -->

</div>

@endif
