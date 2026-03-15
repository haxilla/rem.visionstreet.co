<div class="style2propInfoFrame" style="font-size:1rem"
@if($display=='email')
   style="margin:10px;
   margin-top:15px;"
@endif>
   <div style="padding-left:13px;margin-top:15px;">
      @if($propInfo->xMlsNum)
         <span class="accent_text"
         style="font-weight:bold;
         color:#{{$propInfo->theStyle->accentbars}}">
            MLS#:
         </span>
         <span class="xMlsNum clickable">
            {{$propInfo->xMlsNum}}
         </span>
         @include('flyers.ajaxEdits.xMlsNum')
      @else
         <span class="accent_text"
         style="font-weight:bold;
         color:#{{$propInfo->theStyle->accentbars}}">
            MLS#:
         </span>
         <span class="xMlsNum clickable">
            {{$propInfo->xxMlsNum}}
         </span>
         @include('flyers.ajaxEdits.xMlsNum')
      @endif
   </div>
   <hr style="margin:5px;margin-left:13px;margin-right:13px;height:1px;
   border:none;background-color:#ebebeb;">
   <div style="padding-top:10px;">
      @include('flyers.flyerParts.bulletPoints1')
   </div>
   <div class="dontScalePubRemark"
   style="padding-left:13px;padding-top:20px;">
      <span class="accent_text"
      style="font-weight:bold;color:#{{$propInfo->theStyle->accentbars}}">
         Major Cross Streets:
      </span> {{$propInfo->theMap->xIntersection}}
   </div>
   <hr style="margin:5px;margin-left:13px;margin-right:13px;height:1px;
   border:none;background-color:#ebebeb;">
   <div>
      <div class="dontScalePubRemarks"
      style="line-height:2;max-height:165px;overflow:hidden;
      padding-left:20px;padding-right:20px;margin-top:20px;">
         {{$propInfo->theRemarks->xPubRemarks}}
      </div>
      <div style="text-align:right;padding:20px;
      padding-right:25px;">
         <a href="{{URL::route('public.pubMoreInfo',['enc'=>$enc,])}}"
            style="color:#{{$propInfo->theStyle->accentbars}};font-weight:bold;
            font-size:10pt;" target="_blank"
            class="accent_text"
            data-accenttext="{{$propInfo->theStyle->accentbars}}">
            ...Read More Online
         </a>
      </div>
   </div>
   <hr style="margin:5px;margin-left:13px;margin-right:13px;height:1px;
   border:none;background-color:#ebebeb;">
</div>
