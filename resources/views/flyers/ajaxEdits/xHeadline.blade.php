@if($display=='screen')
  <div
   style="background:#{{$propInfo->theStyle->headline_bar_bg}};
   color:#{{$propInfo->theStyle->headline_bar_text}};
   padding:7px;
   text-align:center;
   font-size:13pt;
   font-weight:bold;
   max-height:65px;
   overflow:hidden;
   position:relative;
   display:none"
   class="xHeadline editable headline_bar_bg headline_bar_text">
      <form class="headlineEditForm"
      style="margin:0;padding:0;display:inline;">
         {{csrf_field()}}
         <input type="text"
         class="flyerFocus"
         name="xHeadline"
         style="background:rgba(255,255,255,.3);border:none;
         border-radius:.5em;padding:3px;text-align:center;width:100%;
         color:#{{$propInfo->theStyle->headline_bar_text}}"
         value="{{$propInfo->theStyle->headline}}">
         <input type="hidden" name="theID"
         value="{{$propInfo->theMeta->sk1}}">
      </form>
   </div>
@endif
