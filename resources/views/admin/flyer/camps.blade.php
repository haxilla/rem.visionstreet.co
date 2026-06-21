@include('public.layout.flyerhead')

<body data-section="admin" class="bg-[#f4f7fb] min-h-screen">

@include('admin.layout.nav')

@php

$propInfo = $data['propInfo'];

$template = strtolower($propInfo->theStyle->template ?? '');
$templateView = 'flyers.s'.$template;

@endphp

<main class="pt-[72px]">

    <div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-6 py-6">

        {{-- HEADER --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 mb-6">

            <div class="flex flex-col gap-2">

                <a href="/admin/campaigns"
                class="text-sm text-[#214e9b] font-semibold">
                    ← Back to Campaigns
                </a>

                <h1 class="text-2xl font-bold text-slate-900">
                    {{ $propInfo->xFullStreet }}
                </h1>

                <div class="text-sm text-slate-500">
                    MLS #{{ $propInfo->xMlsNum }}
                </div>

            </div>

        </div>

        {{-- SUBJECT --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 mb-6">

            <label class="block text-sm font-semibold text-slate-700 mb-2">
                Email Subject
            </label>

            <div class="flex flex-col md:flex-row gap-3">

                <input
                    type="text"
                    value=""
                    class="flex-1 border border-slate-300 rounded-xl px-4 py-3"
                >

                <button
                    class="bg-[#214e9b] text-white px-5 py-3 rounded-xl font-semibold">
                    Save Subject
                </button>

            </div>

        </div>

        {{-- FLYER --}}
        <div class="bg-white rounded-2xl shadow-sm p-4 mb-6">

            <h2 class="font-semibold text-slate-900 mb-4">
                Flyer Preview
            </h2>

            <div class="overflow-x-auto">

                <div class="mx-auto" style="width:600px;">

                    @if(View::exists($templateView))
                        @include($templateView)
                    @endif

                </div>

            </div>

        </div>

        {{-- ACTIVE CAMPAIGNS --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">

            <div class="px-5 py-4 border-b border-slate-200">
                <h2 class="font-semibold text-slate-900">
                    Active Campaigns
                </h2>
            </div>

            <div class="divide-y divide-slate-100">

                {{-- loop active campaigns here --}}

            </div>

        </div>

        {{-- ADD CAMPAIGN --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 mb-6">

            <h2 class="font-semibold text-slate-900 mb-4">
                Add Campaign Area
            </h2>

            <div class="flex flex-col md:flex-row gap-3">

                <select class="flex-1 border border-slate-300 rounded-xl px-4 py-3">
                    <option>Select Area</option>
                </select>

                <button
                    class="bg-[#214e9b] text-white px-5 py-3 rounded-xl font-semibold">
                    Add Campaign
                </button>

            </div>

        </div>

        {{-- COMPLETED --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

            <div class="px-5 py-4 border-b border-slate-200">
                <h2 class="font-semibold text-slate-900">
                    Completed Campaigns
                </h2>
            </div>

            <div class="divide-y divide-slate-100">

                {{-- loop completed campaigns here --}}

            </div>

        </div>

    </div>

</main>

@include('public.layout.footer')

</body>
</html>