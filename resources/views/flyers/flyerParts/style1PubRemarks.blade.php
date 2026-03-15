<div
@if($display=='screen' && $totalPhotos>7)
  class="style1RemarksBackground"
@elseif($display=='screen' && $totalPhotos==7)
  class="style1RemarksBackground7photo"
@elseif($display=='screen' && $totalPhotos==6)
  class="style1RemarksBackground6photo"
@elseif($display=='screen' && $totalPhotos==5)
  class="style1RemarksBackground5photo"
@elseif($display=='screen' && $totalPhotos==4)
  class="style1RemarksBackground4photo"
@endif
@if($display=='email' && $totalPhotos > 7)
  style="padding:15px;padding-top:10px;text-align:left;
    line-height:2.35;overflow:hidden;height:475px;font-size:12pt;
    box-sizing:content-box;padding-top:20px;"
@elseif($display=='email' && $totalPhotos==7)
  style="padding:20px;text-align:left;line-height:1.75;padding-top:15px;
  overflow:hidden;height:305px;font-size:12pt;box-sizing:content-box;"
@elseif($display=='email' && $totalPhotos==6)
  style="padding:15px;padding-top:0;text-align:left;line-height:1.8 !important;
  font-size:11pt !important;padding-top:0;overflow:hidden;height:145px;
  font-size:12pt;box-sizing:content-box;"
@elseif($display=='email' && $totalPhotos==5)
  style="padding:15px;text-align:left;line-height:1.75;padding-bottom:25px;
  padding-top:10px;overflow:hidden;height:195px;font-size:12pt;
  box-sizing:content-box;"
@elseif($display=='email' && $totalPhotos==4)
  style="padding-top:0;padding-bottom:0;padding-left:20px;
  padding-right:20px;text-align:left;line-height:1.55;
  overflow:hidden;height:70px;font-size:12pt;box-sizing:content-box;"
@endif>
  {{$propInfo->theRemarks->xPubRemarks}}
</div>
