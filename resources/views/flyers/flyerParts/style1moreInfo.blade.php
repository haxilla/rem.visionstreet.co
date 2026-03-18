<div style="font-size:9pt !important;font-weight:bold;
text-align:right;padding-right:25px;padding-top:10px;
padding-bottom:10px;text-decoration:underline;">
  <a href="#"
  style="text-decoration:underline;
  font-weight:bold;
  @if($propInfo->theStyle->accentbars=='ffc60b')
    color:#333333;
  @else
    color:#{{$propInfo->theStyle->accentbars}};
  @endif" class="accent_text moreInfoLink" target="_blank" 
  data-accenttext="{{$propInfo->theStyle->accentbars}}">
    ...More Online
  </a>
</div>
