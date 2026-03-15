<div style="font-size:10pt;
color:#333;
padding-left:10px;
padding-right:10px;">
  <div>
    <div style="text-align:center;border:1px solid #{{$propInfo->theStyle->accentbars}};
    margin-top:7px;margin-bottom:7px;padding-top:15px;padding-bottom:15px;
    border-radius:5px;" class="accent_border">
      <a href="{{URL::route('public.pubShowAllPhotos',['enc'=>$enc])}}"
      style="font-weight:bold;text-decoration:none;
      color:#{{$propInfo->theStyle->accentbars}};
      font-size:12pt;"
      class="accent_text"
      target="_blank">
         @if($totalPhotos>1)
            See All {{$totalPhotos}} Photos
         @else
            View Online!
         @endif
      </a>
    </div>
  </div>
  <div style="border:1px solid #eee;background-color:#f9f9f9;
  border-radius:5px;line-height:2.00;height:202px;
  margin-top:7px;overflow:hidden;max-width:195px;">
    <div style="padding:10px;padding-left:5px;padding-top:5px;color:#000;
    font-size:9pt;">
       @if($propInfo->theRemarks->xb1)
          <div style="white-space:nowrap;overflow:hidden;text-overflow: ellipsis;">
             &bull;&nbsp;{{$propInfo->theRemarks->xb1}}
          </div>
       @endif
       @if($propInfo->theRemarks->xb2)
          <div style="white-space:nowrap;overflow:hidden;text-overflow: ellipsis;">
             &bull;&nbsp;{{$propInfo->theRemarks->xb2}}
          </div>
       @endif
       @if($propInfo->theRemarks->xb3)
          <div style="white-space:nowrap;overflow:hidden;text-overflow: ellipsis;">
             &bull;&nbsp;{{$propInfo->theRemarks->xb3}}
          </div>
       @endif
       @if($propInfo->theRemarks->xb4)
          <div style="white-space:nowrap;overflow:hidden;text-overflow: ellipsis;">
             &bull;&nbsp;{{$propInfo->theRemarks->xb4}}
          </div>
       @endif
       @if($propInfo->theRemarks->xb5)
       <div style="white-space:nowrap;overflow:hidden;text-overflow: ellipsis;">
          &bull;&nbsp;{{$propInfo->theRemarks->xb5}}
       </div>
       @endif
       @if($propInfo->theRemarks->xb6)
          <div style="white-space:nowrap;overflow:hidden;text-overflow: ellipsis;">
             &bull;&nbsp;{{$propInfo->theRemarks->xb6}}
          </div>
       @endif
       @if($propInfo->theRemarks->xb7)
          <div style="white-space:nowrap;overflow:hidden;text-overflow: ellipsis;">
             &bull;&nbsp;{{$propInfo->theRemarks->xb7}}
          </div>
       @endif
       @if($propInfo->theRemarks->xb8)
          <div style="white-space:nowrap;overflow:hidden;text-overflow: ellipsis;">
             &bull;&nbsp;{{$propInfo->theRemarks->xb8}}
          </div>
       @endif
    </div>
  </div>
</div>

<!--



<div style="color:#900;font-weight:bold;font-size:8pt;">
<div style="margin-left:25px;width:80px;background-color:#f9f9f9;color:#900;border-radius:10px;padding-top:3px;padding-bottom:3px;margin-bottom:5px;text-align:center;border:1px solid #eee;">
   MLS Link
</div>
<div style="margin-left:25px;width:80px;background-color:#f9f9f9;color:#900;border-radius:10px;padding-top:3px;
padding-bottom:3px;margin-bottom:5px;text-align:center;border:1px solid #eee;">
   Virtual Tour
</div>
</div>
-->
