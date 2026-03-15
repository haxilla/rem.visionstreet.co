@if($propInfo->theRemarks->xb1||
$propInfo->theRemarks->xb2||$propInfo->theRemarks->xb3||
$propInfo->theRemarks->xb4||$propInfo->theRemarks->xb5||
$propInfo->theRemarks->xb6||$propInfo->theRemarks->xb7||
$propInfo->theRemarks->xb8)
   <div style="line-height:{{$bullets_LH}};font-size:11pt;padding-left:10px;
   font-style:italic;text-align:left;font-size:.9rem">
      @if($propInfo->theRemarks->xb1)
         <div>
            &bull;&nbsp; {{$propInfo->theRemarks->xb1}}
         </div>
      @endif
      @if($propInfo->theRemarks->xb2)
         <div>
            &bull;&nbsp; {{$propInfo->theRemarks->xb2}}
         </div>
      @endif
      @if($propInfo->theRemarks->xb3)
         <div>
            &bull;&nbsp; {{$propInfo->theRemarks->xb3}}
         </div>
      @endif
      @if($propInfo->theRemarks->xb4)
         <div>
            &bull;&nbsp; {{$propInfo->theRemarks->xb4}}
         </div>
      @endif
      @if($propInfo->theRemarks->xb5)
         <div>
            &bull;&nbsp; {{$propInfo->theRemarks->xb5}}
         </div>
      @endif
      @if($propInfo->theRemarks->xb6)
         <div>
            &bull;&nbsp; {{$propInfo->theRemarks->xb6}}
         </div>
      @endif
      @if($propInfo->theRemarks->xb7)
         <div>
            &bull;&nbsp; {{$propInfo->theRemarks->xb7}}
         </div>
      @endif
      @if($propInfo->theRemarks->xb8)
         <div>
            &bull;&nbsp; {{$propInfo->theRemarks->xb8}}
         </div>
      @endif
   </div>
@endif
