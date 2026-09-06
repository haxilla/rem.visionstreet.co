@if($display=='screen')
   <span class="xState editable scrollHide" 
   style="display:none">
      <form class="stateEditForm" 
      style="margin:0;padding:0;display:inline;">
         {{csrf_field()}}
         <input type="text"
         class="flyerFocus" 
         name="xState"
         style="background:rgba(255,255,255,.15);border:none;
         border-radius:.5em;padding:3px;text-align:center;width:50px;
         color:#{{$propInfo->theStyle->headline_text}}"
         value="{{$propInfo->xState}}">
         <input type="hidden" name="theID"
         value="{{$propInfo->theMeta->sk1}}">
      </form>
   </span>
@endif