<!--- Links Table double wrapped to get 10px without
affecting photo padding too -->
<div>
<table class="flyerLinksAlt1"
@if($display=='email')
style="width:100%;
text-align:center;
border-bottom:1px solid #eeeeee;"
@endif>
   <tr>
      <td>
         <div style="padding:7px;">
            <div style="">
               <span style="color:#{{$propInfo->theStyle->accentbars}};
               font-weight:bold;text-decoration:none;"
               class="accent_text">
                  MLS#:
               </span>
               {{$propInfo->xMlsNum}}
            </div>
         </div>
      </td>
      @if($propInfo['xMlsLink'])
         <td>
            <div style="padding:7px;">
               <a href="{{URL::route('public.pubMlsLink',['enc'=>$enc,])}}"
               style="color:#{{$propInfo->theStyle->accentbars}};
               font-weight:bold;text-decoration:none;"
               class="accent_text" target="_blank">
                  <u>MLS Link</u>
               </a>
            </div>
         </td>
      @endif
      @if($propInfo['xVirtualTour'])
         <td>
            <div style="padding:7px;">
               <a href="{{URL::route('public.pubVtour',['enc'=>$enc,])}}"
               style="color:#{{$propInfo->theStyle->accentbars}};
               font-weight:bold;text-decoration:none;"
               class="accent_text" target="_blank">
                  <u>Virtual Tour</u>
               </a>
            </div>
         </td>
      @endif
   </tr>
</table>
</div>
<!-- end of links table -->
