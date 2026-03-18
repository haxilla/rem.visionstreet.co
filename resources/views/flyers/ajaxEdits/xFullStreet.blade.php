@if($display=='screen')
   <span class="xFullStreet editable scrollHide" 
   style="display:none">
      <form class="streetEditForm" 
      style="margin:0;padding:0;display:inline;">
         {{csrf_field()}}
         <input type="text"
         class="flyerFocus" 
         name="xFullStreet"
         style="background:rgba(255,255,255,.15);border:none;
         border-radius:.5em;padding:3px;text-align:center;
         color:#{{$propInfo->theStyle->headline_text}}"
         value="{{$propInfo->xFullStreet}}">
         <input type="hidden" name="theID"
         value="{{$propInfo->theMeta->sk1}}">
      </form>
   </span>
@endif