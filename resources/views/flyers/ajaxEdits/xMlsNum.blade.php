@if($display=='screen')
   <span class="xMlsNum editable scrollHide" 
   style="display:none">
      <form class="xMlsNumEditForm" 
      style="margin:0;padding:0;display:inline;">
         {{csrf_field()}}
         <input type="text"
         class="flyerFocus" 
         name="xMlsNum"
         style="background:rgba(0,0,0,.15);border:none;
         border-radius:.5em;padding:3px;text-align:center;
         color:#333;width:100px;"
         value="{{$propInfo->xMlsNum}}">
         <input type="hidden" name="theID"
         value="{{$propInfo->theMeta->sk1}}">
      </form>
   </span>
@endif