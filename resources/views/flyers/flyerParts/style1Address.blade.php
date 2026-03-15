<div
  @if($display=='screen' && $totalPhotos>7)
    class="style1AddressFrame"
  @elseif($display=='screen' && $totalPhotos==7)
    class="style1AddressFrame7photo"
  @elseif($display=='screen' && $totalPhotos==6)
    class="style1AddressFrame6photo"
  @elseif($display=='screen' && $totalPhotos==5)
    class="style1AddressFrame5photo"
  @elseif($display=='screen' && $totalPhotos==4)
    class="style1AddressFrame4photo"
  @elseif($display=='email' && $totalPhotos>7)
    style="box-sizing:content-box;padding-top:5px;
    text-align:center;"
  @elseif($display=='email' && $totalPhotos==7)
    style="box-sizing:content-box;padding-top:5px;
    text-align:center;"
  @elseif($display=='email' && $totalPhotos==6)
    style="padding-top:10px;
    padding-bottom:5px;
    box-sizing:content-box;"
  @elseif($display=='email' && $totalPhotos==5)
    style="box-sizing:content-box;padding-top:10px;text-align:center;"
  @elseif($display=='email' && $totalPhotos==4)
    style="box-sizing:content-box;padding-top:5px;padding-bottom:0;
    text-align:center;"
  @endif>
  <div
    @if($display=='screen')
      class="style1Address"
    @else
      style="font-weight:bold;
      font-size:12pt;"
    @endif>
    {{ $propInfo['xFullStreet'] }}
  </div>
  <div
    @if($display=='screen')
      class="style1AddressCSZ"
    @else
      style="font-size:10pt;"
    @endif>
      {{ $propInfo->xCity}}, {{ $propInfo->xState}}
      @if($propInfo->xZip)
        {{ $propInfo->xZip}}
      @else
        {{ $propInfo->xxZip}}
      @endif
  </div>
  <div
    @if($display=='screen' && $totalPhotos > 7)
      class="style1Address"
      style="font-weight:bold;"
    @elseif($display=='screen' && $totalPhotos==7)
      class="flyerAddress7photo"
      style="font-weight:bold;"
    @elseif($display=='screen' && $totalPhotos==6)
      class="flyerAddress6photo"
      style="font-weight:bold;"
    @elseif($display=='screen' && $totalPhotos==5)
      class="flyerAddress5photo"
      style="font-weight:bold;"
    @elseif($display=='screen' && $totalPhotos==4)
      class="flyerAddress4photo"
      style="font-weight:bold;"
    @endif
    @if($display=='email' && $totalPhotos > 7)
      style="font-weight:bold;
      font-size:12pt;"
    @elseif($display=='email' && $totalPhotos==7)
      style="font-weight:bold;
      font-size:11pt;"
    @elseif($display=='email' && $totalPhotos==6)
      style="font-weight:bold;
      font-size:11pt;"
    @elseif($display=='email' && $totalPhotos==5)
      style="font-weight:bold;
      font-size:11pt;"
    @elseif($display=='email' && $totalPhotos==4)
      style="font-weight:bold;
      font-size:11pt;"
    @endif>
      ${{ number_format($propInfo->xListPrice)}}
  </div>
</div>
