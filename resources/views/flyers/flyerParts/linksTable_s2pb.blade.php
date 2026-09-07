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
               <a href="#"
               style="color:#{{$accentTextColor}};
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
               <a href="@if($display=='email'){{$propInfo->xMlsLink}}@else#@endif"
               style="color:#{{$accentTextColor}};
               font-weight:bold;text-decoration:none;"
               class="accent_text @if($display=='screen') clickable @endif"
               @if($display=='screen') data-modal-trigger="mlslink" @endif
               target="_blank">
                  MLS Link
               </a>
            </div>
         </td>
      @endif
      @if($propInfo['xVirtualTour'])
         <td>
            <div style="padding:7px;">
               <a href="@if($display=='email'){{$propInfo->xVirtualTour}}@else#@endif"
               style="color:#{{$accentTextColor}};
               font-weight:bold;text-decoration:none;"
               class="accent_text @if($display=='screen') clickable @endif"
               @if($display=='screen') data-modal-trigger="virtualtour" @endif
               target="_blank">
                  Virtual Tour
               </a>
            </div>
         </td>
      @endif
   </tr>
</table>
</div>
<!-- end of links table -->
