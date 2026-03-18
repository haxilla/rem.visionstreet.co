<div class="accent_bars"
  style="background-color:#{{$propInfo->theStyle->accentbars}};
  color:#{{$propInfo->theStyle->headline_bar_text}};
  @if($totalPhotos >= 7)
    border-radius:10px;padding:5px;padding-left:10px;
    text-align:center;
  @elseif($totalPhotos==6||$totalPhotos==5)
    margin-top:10px;margin-bottom:10px;padding-top:10px;
    padding-bottom:10px;border-radius:10px;
    text-align:center;
  @elseif($totalPhotos==4)
    margin-top:10px;margin-bottom:10px;padding-top:5px;
    padding-bottom:5px;border-radius:5px;
    text-align:center;
  @endif">
    <div style="display:inline-block;">
      <a href="#"
      style="@if($propInfo->theStyle->accentbars=='ffc60b')
        color:#333333;
      @else
        color:#ffffff;
      @endif
      font-size:9pt;font-weight:bold;
      text-decoration:none;" target="_blank"
      class="accent_link">
          View {{$totalPhotos}} Photos
      </a>
    </div>
    @if($propInfo['xMlsLink'])
      <div style="display:inline-block;
      padding:5px;
      padding-top:0;
      padding-bottom:0;">
        |
      </div>
      <div style="display:inline-block;">
        <a href="{{URL::route('public.pubMlsLink',['enc'=>$enc,])}}"
        style="font-size:10pt;
        color:#{{$propInfo->theStyle->headline_bar_text}};
        text-decoration:none;" target="_blank">
            MLS Link
        </a>
      </div>
    @endif
    @if($propInfo['xVirtualTour'])
      <div class="style1LinkDivider"
      style="display:inline-block;
      padding:5px;
      padding-top:0;
      padding-bottom:0;">
        |
      </div>
      <div style="display:inline-block;">
        <a href="{{URL::route('public.pubVtour',['enc'=>$enc,])}}"
        style="font-size:10pt;
        color:#{{$propInfo->theStyle->headline_bar_text}};
        text-decoration:none;" target="_blank">
            Virtual Tour
        </a>
      </div>
    @endif
  </div>
</div>
