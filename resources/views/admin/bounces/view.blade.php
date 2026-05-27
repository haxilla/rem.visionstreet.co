<div class="p-6 max-w-5xl mx-auto">

    <div class="mb-5">
        <a href="/admin/bounces"
           class="text-blue-700 hover:underline">
            ← Back to Bouncebox
        </a>
    </div>

    <div class="border rounded-lg bg-white shadow-sm overflow-hidden">

        <div class="p-5 border-b bg-gray-50">
            <h1 class="text-xl font-bold">
                {{ iconv_mime_decode($overview->subject ?? '(No subject)', ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8') }}
            </h1>

            <div class="mt-3 text-sm text-gray-600 space-y-1">
                <div>
                    <strong>Message #:</strong>
                    {{ $messageNumber }}
                </div>

                <div>
                    <strong>From:</strong>
                    {{ iconv_mime_decode($overview->from ?? '', ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8') }}
                </div>

                <div>
                    <strong>Date:</strong>
                    {{ $overview->date ?? '' }}
                </div>
            </div>
        </div>

        <div class="p-5">
            <pre class="whitespace-pre-wrap text-sm bg-gray-50 border rounded p-4 overflow-x-auto">{{ $body }}</pre>
        </div>

    </div>

</div>