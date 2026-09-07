
@php
$agentImg = null;
if (!empty($agentInfo->agtPhoto)) {
    $agentPhotoPath = public_path("agentPhotos/{$agentInfo->photoToken()}/{$agentInfo->agtPhoto}");
    if (file_exists($agentPhotoPath)) {
        $agentImg = "{$fromURL}/agentPhotos/{$agentInfo->photoToken()}/{$agentInfo->agtPhoto}";
    }
}
$officeLogo="{$fromURL}/officeLogos/{$officeInfo->officeID}/{$agentInfo->agtLogo}";
@endphp

<div style="background-color:#f9f9f9;line-height:1.45;color:#333;
font-family:arial;@if($display=='screen') cursor:pointer;@endif"
@if($display=='screen') data-modal-trigger="agentcontact" @endif>
<table style="text-align:left;width:100%;">
  <tr>
    <td colspan="4">
      <div class="flyerForMoreInfo"
          style="padding:15px;
          font-size:10pt;">
        For More information contact:
      </div>
    </td>
  </tr>
  <tr>
    @if($agentImg)
    <td class="agentPhotoSection"
        style="vertical-align:bottom;
        width:20%;">
      <div style="width:100%;">
        @if($agentImg)
        <img
          src="{{ $agentImg }}"
          class="agentImage"
            style="display:block;
            max-width:100%;
            max-height:125px;
            padding-left:15px;">
        @endif
      </div>
    </td>
    @endif
    <td class="agentInfoSection"
        style="vertical-align:bottom;
        margin:0;
        padding:0;
        width:50%;
        padding-left:15px;
        font-size:10pt;">
      <div>
        <div>
          <div style="display:inline-block;font-weight:bold;font-size:13pt;" id="bannerAgtFullName">
            {{ $agentInfo->agtFullName }}
          </div>
          <div style="display:inline-block;font-size:8pt;padding-left:5px;" id="bannerAgtDesigs">
            {{ $agentInfo->agtDesigs}}
          </div>
        </div>
        <div style="font-weight:bold;font-size:9pt;" id="bannerOfficeName">
          {{ $officeInfo->officeName }}
        </div>
        <div id="bannerOfficeAddress">
          {{ $officeInfo->officeAddress1 }}
          @if($officeInfo->officeAddress2)
            {{ $officeInfo->officeAddress2 }}
          @endif
        </div>
        <div>
          <div style="display:inline-block;"
          id="bannerOfficeCity">
            {{ $officeInfo->officeCity}},
          </div>
          <div style="display:inline-block;"
          id="bannerOfficeState">
            {{ $officeInfo->officeState }}
          </div>
          <div style="display:inline-block;"
          id="bannerOfficeZip">
            {{ $officeInfo->officeZip }}
          </div>
        </div>
        <div id="bannerAgtMainPhone">
          {{$agentInfo->agtMainPhone}}
        </div>
        <div>
          <a style="color:#333;font-weight:bold;"
            href="#">Email Me
          </a>
        </div>
      </div>
    </td>
    <td style="vertical-align:bottom;width:20%;
    padding-right:15px;text-align:right;">
      <div>
        <div>
          @if($agentInfo->agtLogo)
          <img
              src="{{ $officeLogo }}">
          @endif
        </div>
      </div>
    </td>
    <td style="vertical-align:bottom;width:15%;text-align:center;">
      <div>
        <div style="padding-bottom:10px;">
          <img
            src="{{ $fromURL }}/images/flyerimages/realtorlogo.gif"
            class="flyerIcons"
            @if($display=='email')
              style="max-height:30px;"
            @endif>
        </div>
        <div>
          <img
            src="{{ $fromURL }}/images/flyerimages/fairhousing.gif"
            class="flyerIcons"
            @if($display=='email')
              style="max-height:30px;"
            @endif>
        </div>
      </div>
    </td>
  </tr>
  <tr>
    <td colspan="4" height="10px">
    </td>
  </tr>
</table>
</div>
