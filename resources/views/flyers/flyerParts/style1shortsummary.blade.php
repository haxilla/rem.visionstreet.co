<div
   @if($display=='screen')
      class="style1ShortSummary"
   @elseif($display=='email')
      style="font-size:10pt;
      font-weight:bold;
      box-sizing:content-box;"
   @endif>
   MLS# {{$propInfo->xMlsNum}} |
   @if($propInfo->xxBeds)
      {{$propInfo->xxBeds}}
   @else
      {{$propInfo->xBeds}}
   @endif Bd |
   @if($propInfo->xxBaths)
      {{$propInfo->xxBaths}}
   @else
      {{$propInfo->xBaths}}
   @endif Ba |
   @if($propInfo->xxSqft)
      {{$propInfo->xxSqft}}
   @else
      {{$propInfo->xSqft}}
   @endif Sqft
</div>
