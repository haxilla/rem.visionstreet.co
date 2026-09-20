{{-- Email-safe: tables, inline styles, no external CSS. --}}
@php $first = trim((string) $agent->agtFirst) ?: 'there'; @endphp
<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background:#f1f5f9;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:24px 12px;">
  <tr><td align="center">
    <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;background:#ffffff;border-radius:12px;">
      <tr><td style="padding:28px 32px 8px 32px;font-family:Arial,Helvetica,sans-serif;font-size:22px;font-weight:bold;color:#0d1b3e;">
        Realty<span style="color:#e79a63;">Emails</span>
      </td></tr>
      <tr><td style="padding:8px 32px 0 32px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:24px;color:#334155;">
        <p style="margin:0 0 14px 0;">Hi {{ $first }},</p>
        <p style="margin:0 0 14px 0;">RealtyEmails has a new website. For your security you need to create a new password before you sign in - your old password can't be used.</p>
        <p style="margin:0 0 22px 0;">This link is for you only and works once, for the next {{ $minutes }} minutes:</p>
      </td></tr>
      <tr><td align="center" style="padding:0 32px 22px 32px;">
        <table role="presentation" cellpadding="0" cellspacing="0"><tr>
          <td bgcolor="#214e9b" style="background:#214e9b;border-radius:10px;">
            <a href="{{ $url }}" style="display:inline-block;padding:14px 28px;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:bold;color:#ffffff;text-decoration:none;">Create my new password</a>
          </td>
        </tr></table>
      </td></tr>
      <tr><td style="padding:0 32px 8px 32px;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:20px;color:#64748b;">
        <p style="margin:0 0 12px 0;">If the button doesn't work, copy this address into your browser:<br><span style="word-break:break-all;color:#214e9b;">{{ $url }}</span></p>
        <p style="margin:0 0 12px 0;">Didn't ask for this? You can ignore this email - nothing changes until someone follows the link.</p>
      </td></tr>
      <tr><td style="padding:12px 32px 28px 32px;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#94a3b8;">
        &copy; {{ date('Y') }} RealtyEmails
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
