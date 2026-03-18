<!--
<div style="width:100%;color:#666;">
  <div style="float:left;font-size:.75em;
  padding-top:3px;">
    Edit Headline Graphic
  </div>
  <div style="float:right;text-align:right;">
    <i class="far fa-times-circle"></i>
  </div>
  <div style="clear:both;">
  </div>
</div>
-->
<?php
// set value
$graphic_words=$propInfo->theStyle->graphic_words;
?>
<div class="row noMargin"
style="border:1px solid #eee;">
  <div class="col-8 noPad">
    <div style="height:100%;padding:5px;padding-left:10px;">
      <select id="headlineCaption" name="graphic_words"
      style="padding:15px;font-size:12pt;width:100%;
      height:100%;">
        <option
        value="justlisted"
        @if($graphic_words=='justlisted')
         selected="selected"
        @endif>
         Just Listed
        </option>
        <option value="reduced"
        @if($graphic_words=='reduced')
         selected="selected"
        @endif>
         Reduced
        </option>
        <option value="openhouse"
        @if($graphic_words=='openhouse')
         selected="selected"
        @endif>
         Open House
        </option>
        <option value="backonmarket"
        @if($graphic_words=='backonmarket')
         selected="selected"
        @endif>
         Back on Market
        </option>
        <option value="greatbuy"
        @if($graphic_words=='greatbuy')
         selected="selected"
        @endif>
         Great Buy
        </option>
        <option value="mustsee"
        @if($graphic_words=='mustsee')
         selected="selected"
        @endif>
         Must See
        </option>
        <option value="amazingviews"
        @if($graphic_words=='amazingviews')
         selected="selected"
        @endif>
         Amazing Views
        </option>
        <option value="horseproperty"
        @if($graphic_words=='horseproperty')
         selected="selected"
        @endif>
         Horse Property
        </option>
        <option value="acreage"
        @if($graphic_words=='acreage')
         selected="selected"
        @endif>
         Acreage
        </option>
        <option value="agentbonus"
        @if($graphic_words=='agentbonus')
         selected="selected"
        @endif>
         Agent Bonus
        </option>
        <option value="bankowned"
        @if($graphic_words=='bankowned')
         selected="selected"
        @endif>
         Bank Owned
        </option>
        <option value="modelcloseout"
        @if($graphic_words=='modelcloseout')
         selected="selected"
        @endif>
         Model Closeout
        </option>
      </select>
    </div>
  </div>
  <div class="col-4 noPad"
  style="text-align:center;font-size:.75em;
  padding-top:5px;padding-bottom:5px;">
    <div style="border:1px solid rgb(169, 169, 169);padding:5px 10px;
    margin:5px;margin-right:10px;">
      Underline
    </div>
    <div style="border:1px solid rgb(169, 169, 169);padding:5px 10px;
    margin:5px;margin-right:10px;">
      Bold
    </div>
    <div style="border:1px solid rgb(169, 169, 169);padding:5px 10px;
    margin:5px;margin-right:10px;">
      3D
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div style="background:#ebebeb;text-align:center;">
      <button class="btn btn-secondary"
      style="padding:0 15px !important;
      margin:0;border-radius:.5em;margin:10px;">
        OK
      </button>
    </div>
  </div>
</div>
