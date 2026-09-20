{{-- The flyer as an EMAIL: the flyer's own template (flyers/s1pc ... s5pt) rendered in its
     email mode ($display = 'email' - absolute image and "view online" links, no on-screen
     editing hooks), centred in a 600px column. Used by adminController::sendFlyerEmail
     (Quick Email / Custom Email). Needs $propInfo (loaded by queries/flyerdetails.php) and
     $subject; this file is also reachable by URL through the /admin/{segments} convention,
     where neither exists. --}}
@php abort_unless(isset($propInfo, $subject), 404); @endphp
@php
    include(app_path().'/flyers/variables.php');

    $display      = 'email';
    $templateView = 'flyers.s'.strtolower($propInfo->theStyle->template ?? '');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subject }}</title>
</head>
<body style="margin:0;padding:0;background-color:#ffffff;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
        <tr>
            <td align="center" style="padding:10px 0;">
                <div style="width:600px;max-width:100%;text-align:left;">
                    @if(View::exists($templateView))
                        @include($templateView)
                    @endif
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
