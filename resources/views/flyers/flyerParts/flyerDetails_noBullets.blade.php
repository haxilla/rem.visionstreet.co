<div style="margin:10px;margin-top:15px;">
   <div style="padding-left:13px;">
      @if($propInfo->xMlsNum)
         <span class="accent_text"
         style="color:#{{$propInfo->theStyle->accentbars}};
         font-weight:bold;">MLS#:</span> {{$propInfo->xMlsNum}}
      @else
         <span class="accent_text"
         style="color:#{{$propInfo->theStyle->accentbars}};
         font-weight:bold;">MLS#:</span> {{$propInfo->xxMlsNum}}
      @endif
   </div>
   <div style="padding-left:13px;padding-top:20px;">
      <span class="accent_text"
      style="font-weight:bold;color:#{{$propInfo->theStyle->accentbars}};">
         Major Cross Streets:
      </span>{{$propInfo->theMap->xIntersection}}
   </div>
   <hr style="margin:5px;margin-left:13px;margin-right:13px;">
   <div>
      <div class="dontScalePubRemarks"
      style="line-height:2;max-height:165px;overflow:hidden;
      padding-left:20px;padding-right:20px;margin-top:20px;">
         {{$propInfo->theRemarks->xPubRemarks}}
      </div>
      <div class="dontScalePubRemarks accent_text" style="text-align:right;
      padding:20px;padding-right:25px;">
         <a href="#"
         style="font-weight:bold;color:#{{$propInfo->theStyle->accentbars}}"
         class="accent_text" target="_blank">
            <u>...Read More Online</u>
         </a>
      </div>
   </div>
   <hr style="margin:0;margin-left:13px;margin-right:13px;">
</div>
