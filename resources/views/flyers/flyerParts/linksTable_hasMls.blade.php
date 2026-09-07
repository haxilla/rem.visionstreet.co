<!--- Links Table double wrapped to get 10px without
affecting photo padding too -->
<div>
<table style="width:100%;margin:15px;
margin-top:0;margin-bottom:0;">
   <tr>
      <td>
        @if($propInfo->xMlsNum)
         <div style="float:left;font-size:12pt;"
         @if($display=='screen') class="clickable" data-modal-trigger="mls" @endif>
            <div style="padding:7px;display:inline-block">
               <div>
                  <span style="color:#{{$accentTextColor}};
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
                  <a href="@if($display=='email'){{$propInfo->xVirtualTour}}@else#@endif"
                  style="color:#{{$accentTextColor}};
                  font-weight:bold;text-decoration:none;
                  font-size:10pt;"
                  class="accent_text @if($display=='screen') clickable @endif" target="_blank"
                  @if($display=='screen') data-modal-trigger="virtualtour" @endif>
                     <u>Virtual Tour</u>
                  </a>
               </div>
            @endif
            @if($propInfo['xMlsLink'])
               <div style="padding:7px;display:inline-block;">
                  <a href="@if($display=='email'){{$propInfo->xMlsLink}}@else#@endif"
                  style="color:#{{$accentTextColor}};
                  font-weight:bold;text-decoration:none;font-size:10pt;"
                  class="accent_text @if($display=='screen') clickable @endif" target="_blank"
                  @if($display=='screen') data-modal-trigger="mlslink" @endif>
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
