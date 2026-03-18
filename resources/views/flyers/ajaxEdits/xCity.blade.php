@if($display=='screen')
   <span class="xCity editable scrollHide" 
   style="display:none">
      <form class="cityEditForm" 
      style="margin:0;padding:0;display:inline;">
         {{csrf_field()}}
         <input type="text"
         class="flyerFocus" 
         name="xCity"
         style="background:rgba(255,255,255,.15);border:none;
         border-radius:.5em;padding:3px;text-align:center;
         color:#{{$propInfo->theStyle->headline_text}}"
         value="{{$propInfo->xCity}}">
         <input type="hidden" name="theID"
         value="{{$propInfo->theMeta->sk1}}">
      </form>
   </span>
@endif