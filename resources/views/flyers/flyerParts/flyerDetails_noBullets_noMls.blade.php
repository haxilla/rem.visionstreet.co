<div class="style2propInfoFrame"
@if($display=='email')
   style="margin:10px;margin-top:15px;"
@endif>
   <div class="dontScalePubRemarks" style="padding-left:13px;">
      <span class="accent_text"
      style="font-weight:bold;color:#{{$propInfo->theStyle->accentbars}};">
         Major Cross Streets:
      </span>{{$propInfo->theMap->xIntersection}}
   </div>
   <hr style="margin:5px;margin-left:13px;margin-right:13px;height:1px;
   border:none;background-color:#ebebeb;">
   <div>
      <div class="dontScalePubRemarks"
      style="line-height:2;max-height:165px;overflow:hidden;
      padding-left:20px;padding-right:20px;margin-top:20px;">
         {{$propInfo->theRemarks->xPubRemarks}}
      </div>
      <div class="dontScalePubRemarks accent_text" style="text-align:right;
      padding:20px;padding-right:25px;">
         <a href="#"
         style="font-weight:bold;color:#{{$propInfo->theStyle->accentbars}};
         font-size:10pt;"
         class="accent_text" target="_blank">
            ...Read More Online
         </a>
      </div>
   </div>
   <hr style="margin:5px;margin-left:13px;margin-right:13px;height:1px;
   border:none;background-color:#ebebeb;">
</div>
