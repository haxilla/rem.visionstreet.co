<div class="p-6 max-w-7xl mx-auto">

    <div class="mb-5">
        <a href="/admin/bounces"
           class="text-blue-700 hover:underline">
            ← Back to Bouncebox
        </a>
    </div>

    <div class="border rounded-lg bg-white shadow-sm overflow-hidden">

        <div class="p-5 border-b bg-gray-50">

            <h1 class="text-xl font-bold break-words">
                {{ iconv_mime_decode($overview->subject ?? '(No subject)', ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8') }}
            </h1>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-700">

                <div>
                    <strong>Message #:</strong>
                    {{ $messageNumber }}
                </div>

                <div>
                    <strong>Date:</strong>
                    {{ $overview->date ?? '' }}
                </div>

                <div class="md:col-span-2 break-all">
                    <strong>From:</strong>

                    {{ iconv_mime_decode(
                        $overview->from ?? '',
                        ICONV_MIME_DECODE_CONTINUE_ON_ERROR,
                        'UTF-8'
                    ) }}
                </div>

                @if(!empty($overview->to))
                    <div class="md:col-span-2 break-all">
                        <strong>To:</strong>

                        {{ iconv_mime_decode(
                            $overview->to,
                            ICONV_MIME_DECODE_CONTINUE_ON_ERROR,
                            'UTF-8'
                        ) }}
                    </div>
                @endif

            </div>

        </div>

        <div class="bg-gray-100 p-4">

            @if($bodyType === 'html')

                <iframe
                    class="w-full bg-white border rounded"
                    style="height:1400px;max-width:900px;"
                    sandbox="allow-same-origin"
                    srcdoc="{{ $body }}">
                </iframe>

            @else

                <pre class="whitespace-pre-wrap text-sm bg-white border rounded p-4 overflow-x-auto">{{ $body }}</pre>

            @endif

        </div>

    </div>

</div>