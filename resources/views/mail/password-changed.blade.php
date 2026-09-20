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
      <tr><td style="padding:8px 32px 24px 32px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:24px;color:#334155;">
        <p style="margin:0 0 14px 0;">Hi {{ $first }},</p>
        <p style="margin:0 0 14px 0;">The password for your RealtyEmails account ({{ $agent->xxAgtUname }}) was just changed.</p>
        <p style="margin:0;"><strong>If this was you, there's nothing more to do.</strong> If it wasn't, please contact RealtyEmails support right away.</p>
      </td></tr>
      <tr><td style="padding:0 32px 28px 32px;font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#94a3b8;">
        &copy; {{ date('Y') }} RealtyEmails
      </td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
