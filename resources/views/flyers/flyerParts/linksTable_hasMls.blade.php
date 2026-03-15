<!--- Links Table double wrapped to get 10px without
affecting photo padding too -->
<div>
<table style="width:100%;margin:15px;
margin-top:0;margin-bottom:0;">
   <tr>
      <td>
        @if($propInfo->xMlsNum)
         <div style="float:left;font-size:12pt;">
            <div style="padding:7px;display:inline-block">
               <div>
                  <span style="color:#{{$propInfo->theStyle->accentbars}};
                  font-weight:bold;text-decoration:none;"
                  class="accent_text">
                     MLS#:
                  </span>
                  {{$propInfo->xMlsNum}}
               </div>
            </div>
         </div>
         @endif
         <div style="float:right;padding-right:25px;">
            @if($propInfo['xVirtualTour'])
               <div style="padding:7px;display:inline-block;">
                  <a href="{{URL::route('public.pubVtour',['enc'=>$enc,])}}"
                  style="color:#{{$propInfo->theStyle->accentbars}};
                  font-weight:bold;text-decoration:none;
                  font-size:10pt;"
                  class="accent_text" target="_blank">
                     <u>Virtual Tour</u>
                  </a>
               </div>
            @endif
            @if($propInfo['xMlsLink'])
               <div style="padding:7px;display:inline-block;">
                  <a href="{{URL::route('public.pubMlsLink',['enc'=>$enc,])}}"
                  style="color:#{{$propInfo->theStyle->accentbars}};
                  font-weight:bold;text-decoration:none;font-size:10pt;"
                  class="accent_text" target="_blank">
                     <u>MLS Link</u>
                  </a>
               </div>
            @endif
         </div>
         <div style="clear:both;">
         </div>
      </td>
   </tr>
</table>
</div>
<!-- end of links table -->
