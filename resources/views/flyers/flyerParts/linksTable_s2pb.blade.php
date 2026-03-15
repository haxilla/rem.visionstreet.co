<!--- Links Table double wrapped to get 10px without
affecting photo padding too -->
<div>
<table style="width:100%;
text-align:center;
background-color:#f9f9f9;">
   <tr>
      @if($totalPhotos>1)
         <td>
            <div style="padding:7px;">
               <a href="{{URL::route('public.pubShowAllPhotos',['enc'=>$enc,])}}"
               style="color:#{{$propInfo->theStyle->accentbars}};
               font-weight:bold;text-decoration:none;"
               class="accent_text" target="_blank">
                  See All {{$totalPhotos}} Photos
               </a>
            </div>
         </td>
      @endif
      @if($propInfo['xMlsLink'])
         <td>
            <div style="padding:7px;">
               <a href="{{URL::route('public.pubMlsLink',['enc'=>$enc,])}}"
               style="color:#{{$propInfo->theStyle->accentbars}};
               font-weight:bold;text-decoration:none;"
               class="accent_text" target="_blank">
                  MLS Link
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
                  Virtual Tour
               </a>
            </div>
         </td>
      @endif
   </tr>
</table>
</div>
<!-- end of links table -->
