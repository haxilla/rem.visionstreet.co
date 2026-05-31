<div style="padding:24px; max-width:1400px; margin:0 auto; font-family:Arial, sans-serif;">

    <div style="margin-bottom:18px;">
        <a href="/admin/bounces" style="color:#1d4ed8; text-decoration:underline;">
            ← Back to Bouncebox
        </a>
    </div>

    <h1 style="font-size:28px; line-height:1.2; margin:0 0 18px 0;">
        {{ iconv_mime_decode($overview->subject ?? '(No subject)', ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8') }}
    </h1>

    <div style="border:1px solid #ddd; background:#f8fafc; padding:14px; margin-bottom:18px; font-size:14px; line-height:1.5;">
        <div><strong>Message #:</strong> {{ $messageNumber }}</div>
        <div><strong>Date:</strong> {{ $overview->date ?? '' }}</div>
        <div><strong>From:</strong> {{ iconv_mime_decode($overview->from ?? '', ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8') }}</div>

        @if(!empty($overview->to))
            <div><strong>To:</strong> {{ iconv_mime_decode($overview->to, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8') }}</div>
        @endif
    </div>

    </div>

    {{-- INSERT THE NEW REVIEW FORM BLOCK HERE --}}

    <div style="max-width:1200px; margin:0 auto 18px auto;">

        <div style="border:1px solid #ddd; background:#ffffff; padding:14px; font-size:14px;">

            <h2 style="font-size:18px; margin:0 0 10px 0;">
                Find Email
            </h2>

            <form method="POST"
                action="/admin/bounces/find"
                style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">

                @csrf

                <input
                    type="email"
                    name="email"
                    placeholder="Enter recipient email"
                    value="{{ old('email') }}"
                    required
                    style="width:420px; max-width:100%; padding:8px 10px; border:1px solid #ccc; border-radius:4px;"
                >

                <input
                    type="hidden"
                    name="messageNumber"
                    value="{{ $messageNumber }}"
                >

                <button
                    type="submit"
                    style="padding:8px 14px; background:#1d4ed8; color:white; border:0; border-radius:4px; cursor:pointer;">
                    Submit
                </button>

            </form>

        </div>

    </div>

    <div style="max-width:1200px; margin:0 auto 18px auto;">

        <h2 style="font-size:20px; margin:22px 0 10px;">
            Rendered Body
        </h2>

        @if(trim($body ?? '') !== '')

            @if($bodyType === 'html')
                <div style="width:100%; overflow:auto; background:#f3f4f6; padding:16px; border:1px solid #ddd;">
                    <iframe
                        style="display:block; width:100%; min-width:100%; max-width:100%; height:1400px; border:1px solid #ddd; background:white;"
                        sandbox="allow-same-origin"
                        srcdoc="{{ $body }}">
                    </iframe>
                </div>
            @else
                <pre style="white-space:pre-wrap; font-size:13px; background:#fff; border:1px solid #ddd; padding:14px; overflow:auto;">{{ $body }}</pre>
            @endif

        @else
            <div style="background:#fff7ed; border:1px solid #fed7aa; padding:14px;">
                No normal rendered body was found. Check MIME parts, headers, and raw body below.
            </div>
        @endif

    </div>

    <h2 style="font-size:20px; margin:28px 0 10px;">MIME Parts / Attachments</h2>

    <div style="border:1px solid #ddd; overflow:auto; margin-bottom:22px;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="background:#f1f5f9;">
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Part</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Type</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Encoding</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Filename</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Bytes</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #ddd;">Preview</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($partsReport ?? []) as $part)
                    <tr>
                        <td style="padding:8px; border-bottom:1px solid #eee;">{{ $part['partNumber'] }}</td>
                        <td style="padding:8px; border-bottom:1px solid #eee;">{{ $part['type'] }}/{{ $part['subtype'] }}</td>
                        <td style="padding:8px; border-bottom:1px solid #eee;">{{ $part['encoding'] }}</td>
                        <td style="padding:8px; border-bottom:1px solid #eee;">{{ $part['filename'] }}</td>
                        <td style="padding:8px; border-bottom:1px solid #eee;">{{ number_format($part['bytes']) }}</td>
                        <td style="padding:8px; border-bottom:1px solid #eee; max-width:500px;">
                            <pre style="white-space:pre-wrap; margin:0; font-size:12px;">{{ $part['preview'] }}</pre>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <details style="margin-bottom:18px;">
        <summary style="cursor:pointer; font-weight:bold; font-size:18px;">Raw Headers</summary>
        <pre style="white-space:pre-wrap; font-size:12px; background:#111827; color:#e5e7eb; border:1px solid #374151; padding:14px; overflow:auto;">{{ $headers }}</pre>
    </details>

    <details style="margin-bottom:18px;">
        <summary style="cursor:pointer; font-weight:bold; font-size:18px;">Raw Body</summary>
        <pre style="white-space:pre-wrap; font-size:12px; background:#fff; border:1px solid #ddd; padding:14px; overflow:auto;">{{ $rawBody }}</pre>
    </details>

    <details style="margin-bottom:18px;">
        <summary style="cursor:pointer; font-weight:bold; font-size:18px;">Raw Structure</summary>
        <pre style="white-space:pre-wrap; font-size:12px; background:#f8fafc; border:1px solid #ddd; padding:14px; overflow:auto;">{{ print_r($structure, true) }}</pre>
    </details>

</div>