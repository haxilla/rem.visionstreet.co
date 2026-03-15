<div
  @if($display=='screen' && $totalPhotos>7)
    class="style1MlsNum"
  @elseif($display=='screen' && $totalPhotos==7)
    style="display:none;"
  @elseif($display=='screen' && $totalPhotos==6)
    style="display:none;"
  @elseif($display=='screen' && $totalPhotos==5)
    style="display:none;"
  @elseif($display=='screen' && $totalPhotos==4)
    style="display:none;"
  @endif
  @if($display=='email' && $totalPhotos>7)
    style="font-size:9pt;font-weight:bold;
    margin-bottom:5px;text-align:center;"
  @elseif($display=='email' && $totalPhotos==7)
    style="display:none;"
  @elseif($display=='email' && $totalPhotos==6)
    style="display:none;"
  @elseif($display=='email' && $totalPhotos==5)
    style="display:none;"
  @elseif($display=='email' && $totalPhotos==4)
    style="display:none;"
  @endif>
  MLS#: {{ $propInfo['xMlsNum'] }}
</div>
