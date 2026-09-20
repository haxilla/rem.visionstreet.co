
@php
$agentImg = null;
if (!empty($agentInfo->agtPhoto)) {
    $agentPhotoPath = public_path("agentPhotos/{$agentInfo->photoToken()}/{$agentInfo->agtPhoto}");
    if (file_exists($agentPhotoPath)) {
        $agentImg = "{$fromURL}/agentPhotos/{$agentInfo->photoToken()}/{$agentInfo->agtPhoto}";
    }
}
$officeLogo="{$fromURL}/officeLogos/{$officeInfo->officeID}/{$agentInfo->agtLogo}";

// In an email, Gmail turns the office address and the phone number (and even "AZ" in the
// office name) into blue map / phone links of its own. Text already inside a link is left
// alone, so in an email each of those is wrapped in a link of ours, styled as plain text
// (flyerParts/noAutoLink). They go to the agent's own page (their agent_slug) - or, until they
// have one, to this flyer's page - the phone dials (tel:) and "Email Me" writes to them.
// On screen none of this applies.
$bannerContact = ($display ?? 'screen') === 'email'
    ? \App\Support\AgentSlug::contactFor($agentInfo->id)
    : ['slug' => null, 'email' => null];

$bannerHome = ($fromURL ?? '') . ($bannerContact['slug']
    ? '/' . $bannerContact['slug']
    : (!empty($propInfo->url_slug) ? '/homedetails/' . $propInfo->url_slug : ''));

$bannerPhone = preg_replace('/[^0-9+]/', '', (string) $agentInfo->agtMainPhone);
$bannerMail  = $bannerContact['email']
    ? 'mailto:' . $bannerContact['email'] . '?subject=' . rawurlencode('About ' . ($propInfo->xFullStreet ?? 'your listing'))
    : null;
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
        <div style="font-weight:bold;font-size:10pt;" id="bannerOfficeName">
          @include('flyers.flyerParts.noAutoLink', ['color' => '#333333', 'href' => $bannerHome, 'text' => $officeInfo->officeName])
        </div>
        <div id="bannerOfficeAddress">
          @include('flyers.flyerParts.noAutoLink', ['color' => '#333333', 'href' => $bannerHome, 'text' => $officeInfo->officeAddress1])
          @if($officeInfo->officeAddress2)
            @include('flyers.flyerParts.noAutoLink', ['color' => '#333333', 'href' => $bannerHome, 'text' => $officeInfo->officeAddress2])
          @endif
        </div>
        <div>
          <div style="display:inline-block;"
          id="bannerOfficeCity">
            @include('flyers.flyerParts.noAutoLink', ['color' => '#333333', 'href' => $bannerHome, 'text' => $officeInfo->officeCity . ','])
          </div>
          <div style="display:inline-block;"
          id="bannerOfficeState">
            @include('flyers.flyerParts.noAutoLink', ['color' => '#333333', 'href' => $bannerHome, 'text' => $officeInfo->officeState])
          </div>
          <div style="display:inline-block;"
          id="bannerOfficeZip">
            @include('flyers.flyerParts.noAutoLink', ['color' => '#333333', 'href' => $bannerHome, 'text' => $officeInfo->officeZip])
          </div>
        </div>
        <div id="bannerAgtMainPhone">
          @include('flyers.flyerParts.noAutoLink', ['color' => '#333333', 'href' => $bannerPhone !== '' ? 'tel:' . $bannerPhone : $bannerHome, 'text' => $agentInfo->agtMainPhone])
        </div>
        <div>
          <a style="color:#333;font-weight:bold;"
            href="@if($display=='email' && $bannerMail){{ $bannerMail }}@else#@endif">Email Me
          </a>
        </div>
      </div>
    </td>
    <td style="vertical-align:bottom;width:20%;
    padding-right:15px;text-align:right;">
      <div>
        <div>
          @if($agentInfo->agtLogo)
          {{-- capped to its cell, so an oversized logo can't stretch the flyer --}}
          <img
              src="{{ $officeLogo }}"
              style="max-width:100%;height:auto;">
          @endif
        </div>
      </div>
    </td>
    <td style="vertical-align:bottom;width:15%;text-align:center;">
      <div>
        <div style="padding-bottom:10px;">
          <img
            src="{{ $fromURL }}/images/flyerimages/realtorlogo.gif"
            style="max-height:30px;">
        </div>
        <div>
          <img
            src="{{ $fromURL }}/images/flyerimages/fairhousing.gif"
            style="max-height:30px;">
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
