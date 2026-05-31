@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('public.layout.nav')

<main
    class="transition-all duration-300 min-h-screen pt-24 relative"
    :class="collapsed ? 'ml-20' : 'ml-64'"
>
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
        <div class="pageswap p-6 w-full">

            <div class="min-h-screen bg-[#f4f7fb]">
                <div class="flex min-h-screen">

                    @include('admin.includes.sidebar')

                    <main class="flex-1 px-6 py-6 lg:px-10 lg:py-8">

                        <div class="mb-5">
                            <a
                                href="/admin/bounces"
                                class="inline-flex items-center rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:border-[#214e9b]/40 hover:text-[#214e9b]"
                            >
                                ← Back to Bouncebox
                            </a>
                        </div>

                        {{-- HEADER --}}
                        <div class="rounded-[24px] bg-white px-8 py-7 shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                            <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
                                Bounce Message
                            </div>

                            <h1 class="mt-2 break-words text-[28px] font-semibold leading-tight text-slate-900">
                                {{ iconv_mime_decode($overview->subject ?? '(No subject)', ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8') }}
                            </h1>

                            <div class="mt-6 grid gap-3 text-sm text-slate-600 lg:grid-cols-2">

                                <div>
                                    <span class="font-semibold text-slate-400">Message #:</span>
                                    <span class="ml-1 text-slate-700">{{ $messageNumber }}</span>
                                </div>

                                <div>
                                    <span class="font-semibold text-slate-400">Date:</span>
                                    <span class="ml-1 text-slate-700">{{ $overview->date ?? '' }}</span>
                                </div>

                                <div class="lg:col-span-2">
                                    <span class="font-semibold text-slate-400">From:</span>
                                    <span class="ml-1 text-slate-700">
                                        {{ iconv_mime_decode($overview->from ?? '', ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8') }}
                                    </span>
                                </div>

                                @if(!empty($overview->to))
                                    <div class="lg:col-span-2">
                                        <span class="font-semibold text-slate-400">To:</span>
                                        <span class="ml-1 text-slate-700">
                                            {{ iconv_mime_decode($overview->to, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8') }}
                                        </span>
                                    </div>
                                @endif

                            </div>

                        </div>

                        {{-- FIND EMAIL --}}
                        <div class="mt-8 rounded-[24px] bg-white px-8 py-7 shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                            <div class="mb-5">
                                <h2 class="text-xl font-semibold text-slate-900">
                                    Find Email
                                </h2>

                                <p class="mt-1 text-sm text-slate-500">
                                    Check which list contains the recipient email.
                                </p>
                            </div>

                            <form
                                method="POST"
                                action="/admin/bounces/find"
                                class="flex flex-col gap-3 sm:flex-row sm:items-center"
                            >
                                @csrf

                                <input
                                    type="email"
                                    name="email"
                                    placeholder="Enter recipient email"
                                    value="{{ old('email') }}"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#214e9b]/50 focus:bg-white focus:ring-4 focus:ring-[#214e9b]/10 sm:max-w-lg"
                                >

                                <input
                                    type="hidden"
                                    name="messageNumber"
                                    value="{{ $messageNumber }}"
                                >

                                <button
                                    type="submit"
                                    class="rounded-full bg-[#214e9b] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#1b4386]"
                                >
                                    Submit
                                </button>

                            </form>

                        </div>

                        {{-- RENDERED BODY --}}
                        <div class="mt-8 overflow-hidden rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                                <h2 class="text-sm font-semibold text-slate-700">
                                    Rendered Body
                                </h2>
                            </div>

                            <div class="p-6">

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
                                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm font-medium text-amber-800">
                                        No normal rendered body was found. Check MIME parts, headers, and raw body below.
                                    </div>
                                @endif

                            </div>

                        </div>

                        {{-- MIME PARTS --}}
                        <div class="mt-8 overflow-hidden rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                                <h2 class="text-sm font-semibold text-slate-700">
                                    MIME Parts / Attachments
                                </h2>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">

                                    <thead class="border-b border-slate-200 bg-white text-xs uppercase tracking-wide text-slate-400">
                                        <tr>
                                            <th class="px-5 py-4 text-left">Part</th>
                                            <th class="px-5 py-4 text-left">Type</th>
                                            <th class="px-5 py-4 text-left">Encoding</th>
                                            <th class="px-5 py-4 text-left">Filename</th>
                                            <th class="px-5 py-4 text-left">Bytes</th>
                                            <th class="px-5 py-4 text-left">Preview</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-slate-100">

                                        @foreach(($partsReport ?? []) as $part)

                                            <tr class="align-top transition hover:bg-[#214e9b]/5">
                                                <td class="px-5 py-4 text-slate-500">
                                                    {{ $part['partNumber'] }}
                                                </td>

                                                <td class="px-5 py-4 text-slate-700">
                                                    {{ $part['type'] }}/{{ $part['subtype'] }}
                                                </td>

                                                <td class="px-5 py-4 text-slate-600">
                                                    {{ $part['encoding'] }}
                                                </td>

                                                <td class="px-5 py-4 text-slate-700">
                                                    {{ $part['filename'] }}
                                                </td>

                                                <td class="px-5 py-4 whitespace-nowrap text-slate-600">
                                                    {{ number_format($part['bytes']) }}
                                                </td>

                                                <td class="px-5 py-4 max-w-xl">
                                                    <pre class="m-0 whitespace-pre-wrap rounded-xl bg-slate-50 p-3 text-xs text-slate-600">{{ $part['preview'] }}</pre>
                                                </td>
                                            </tr>

                                        @endforeach

                                    </tbody>

                                </table>
                            </div>

                        </div>

                        {{-- RAW SECTIONS --}}
                        <div class="mt-8 space-y-4">

                            <details class="overflow-hidden rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]">
                                <summary class="cursor-pointer border-b border-slate-200 bg-slate-50 px-6 py-5 text-sm font-semibold text-slate-700">
                                    Raw Headers
                                </summary>

                                <div class="p-6">
                                    <pre class="overflow-auto whitespace-pre-wrap rounded-2xl border border-slate-700 bg-slate-950 p-4 text-xs text-slate-100">{{ $headers }}</pre>
                                </div>
                            </details>

                            <details class="overflow-hidden rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]">
                                <summary class="cursor-pointer border-b border-slate-200 bg-slate-50 px-6 py-5 text-sm font-semibold text-slate-700">
                                    Raw Body
                                </summary>

                                <div class="p-6">
                                    <pre class="overflow-auto whitespace-pre-wrap rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs text-slate-700">{{ $rawBody }}</pre>
                                </div>
                            </details>

                            <details class="overflow-hidden rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]">
                                <summary class="cursor-pointer border-b border-slate-200 bg-slate-50 px-6 py-5 text-sm font-semibold text-slate-700">
                                    Raw Structure
                                </summary>

                                <div class="p-6">
                                    <pre class="overflow-auto whitespace-pre-wrap rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs text-slate-700">{{ print_r($structure, true) }}</pre>
                                </div>
                            </details>

                        </div>

                    </main>

                </div>
            </div>

        </div>
    </div>
</main>

@include('public.layout.footer')

</body>
</html>