@include('public.layout.flyerhead')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')

@php

    $propInfo = $data['propInfo'];

    include(app_path().'/flyers/variables.php');

    $template = strtolower(
        $propInfo->theStyle->template ?? ''
    );

@endphp
<main
    class="transition-all duration-300 min-h-screen pt-24 relative"
    :class="collapsed ? 'ml-20' : 'ml-64'"
>
    <div class="mx-0 sm:mx-4 lg:mx-10">
        <div class="pageswap p-0 sm:p-4 lg:p-6 w-full">

            <div class="min-h-screen bg-[#f4f7fb]">
                <div class="flex min-h-screen">

                    {{-- DESKTOP SIDEBAR --}}
                    <div class="hidden lg:block">
                        @include('admin.includes.sidebar')
                    </div>

                    {{-- MAIN --}}
                    <div class="flex-1 px-2 py-4 sm:px-4 lg:px-10 lg:py-8">

                        {{-- HEADER --}}
                        <div class="rounded-[20px] bg-white px-4 py-5 sm:px-6 sm:py-6 lg:px-8 lg:py-7 shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                            <div class="flex items-center justify-between">

                                <div>
                                    <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
                                        Property Flyer
                                    </div>

                                    <h1 class="mt-2 text-[26px] font-semibold text-slate-900 sm:text-[32px]">
                                        123 Main Street
                                    </h1>

                                    <p class="mt-2 text-[14px] text-slate-600">
                                        MLS #1234567
                                    </p>
                                </div>

                                <a href="#" class="rounded-xl bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">
                                    Back to Campaigns
                                </a>

                            </div>

                        </div>

                        {{-- SUBJECT --}}
                        <div class="mt-6 rounded-[24px] bg-white p-6 shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                            <div class="mb-4">
                                <h2 class="text-lg font-semibold text-slate-900">
                                    Email Subject
                                </h2>
                            </div>

                            <div class="flex flex-col gap-4 lg:flex-row">

                                <input
                                    type="text"
                                    name="emSubject"
                                    value=""
                                    class="flex-1 rounded-xl border border-slate-300 px-4 py-3 text-sm"
                                >

                                <button
                                    type="button"
                                    class="rounded-xl bg-[#214e9b] px-6 py-3 text-sm font-semibold text-white"
                                >
                                    Save Subject
                                </button>

                            </div>

                        </div>

                        {{-- FLYER --}}
                        <div class="mt-6 rounded-[24px] bg-white p-6 overflow-hidden shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                            <div class="mb-6">
                                <h2 class="text-lg font-semibold text-slate-900">
                                    Flyer Preview
                                </h2>
                            </div>

                            <div class="flyer-stage">

                                <div id="flyer-scale-wrapper">

                                    @php
                                        $template = strtolower($propInfo->theStyle->template ?? '');
                                        $templateView = 'flyers.s' . $template;
                                    @endphp

                                    @if(View::exists($templateView))
                                        <div class="flyer-panel active">
                                            @include($templateView)
                                        </div>
                                    @else
                                        <div class="rounded-xl border border-red-300 bg-red-50 p-6 text-center text-red-600">
                                            Missing template: {{ $templateView }}
                                        </div>
                                    @endif

                                </div>

                            </div>

                        </div>

                        {{-- ACTIVE CAMPAIGNS --}}
                        <div class="mt-6 rounded-[24px] bg-white overflow-hidden shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                            <div class="border-b border-slate-200 px-6 py-4">
                                <h2 class="text-lg font-semibold text-slate-900">
                                    Active Campaigns
                                </h2>
                            </div>

                            <div class="hidden lg:flex items-center gap-4 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 border-b border-slate-200">

                                <div class="w-40">
                                    Area
                                </div>

                                <div class="w-28 text-right">
                                    Emails
                                </div>

                                <div class="w-40 text-center">
                                    Status
                                </div>

                                <div class="w-24 text-center">
                                    Links
                                </div>

                            </div>

                            {{-- SAMPLE ROW --}}
                            <div class="border-b border-slate-100 px-4 py-3 hover:bg-slate-50">

                                <div class="hidden lg:flex items-center gap-4">

                                    <div class="w-40">
                                        Phoenix Metro
                                    </div>

                                    <div class="w-28 text-right">
                                        8,442
                                    </div>

                                    <div class="w-40 text-center">
                                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                            In Progress
                                        </span>
                                    </div>

                                    <div class="w-24 text-center">
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-white">
                                            ✓
                                        </span>
                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- ADD CAMPAIGN --}}
                        <div class="mt-6 rounded-[24px] bg-white p-6 shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                            <h2 class="mb-4 text-lg font-semibold text-slate-900">
                                Add Campaign Area
                            </h2>

                            <div class="flex flex-col gap-4 lg:flex-row">

                                <select
                                    class="flex-1 rounded-xl border border-slate-300 px-4 py-3 text-sm"
                                >
                                    <option>Select Area</option>
                                    <option>Phoenix Metro</option>
                                    <option>East Valley</option>
                                    <option>West Valley</option>
                                    <option>North Phoenix</option>
                                    <option>Scottsdale</option>
                                </select>

                                <button
                                    type="button"
                                    class="rounded-xl bg-[#214e9b] px-6 py-3 text-sm font-semibold text-white"
                                >
                                    Add Campaign
                                </button>

                            </div>

                        </div>

                        {{-- COMPLETED --}}
                        <div class="mt-6 rounded-[24px] bg-white overflow-hidden shadow-[0_12px_35px_rgba(15,23,42,0.06)]">

                            <div class="border-b border-slate-200 px-6 py-4">
                                <h2 class="text-lg font-semibold text-slate-900">
                                    Completed Campaign History
                                </h2>
                            </div>

                            <div class="hidden lg:flex items-center gap-4 px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500 border-b border-slate-200">

                                <div class="w-40">
                                    Area
                                </div>

                                <div class="w-28 text-right">
                                    Emails
                                </div>

                                <div class="w-40 text-center">
                                    Finished
                                </div>

                            </div>

                            <div class="border-b border-slate-100 px-4 py-3 hover:bg-slate-50">

                                <div class="hidden lg:flex items-center gap-4">

                                    <div class="w-40">
                                        Phoenix Metro
                                    </div>

                                    <div class="w-28 text-right">
                                        8,442
                                    </div>

                                    <div class="w-40 text-center">
                                        Jun 18, 2026
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</main>

@include('public.layout.footer')

<style>
    .flyer-panel {
        display: block;
    }

    .flyer-stage {
        width: 100%;
        overflow: hidden;
        filter: drop-shadow(0 10px 25px rgba(0,0,0,.12));
    }

    #flyer-scale-wrapper {
        width: 600px;
        transform-origin: top left;
        margin: 0 auto;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    function scaleFlyer() {

        const stage = document.querySelector('.flyer-stage');
        const wrapper = document.getElementById('flyer-scale-wrapper');

        if (!stage || !wrapper) return;

        const activeFlyer = wrapper.querySelector('.flyer-panel.active');

        if (!activeFlyer) return;

        const availableWidth = stage.clientWidth;
        const scale = Math.min(availableWidth / 600, 1);

        wrapper.style.transformOrigin = 'top left';
        wrapper.style.transform = `scale(${scale})`;

        wrapper.style.height = (activeFlyer.offsetHeight * scale) + 'px';
    }

    scaleFlyer();
    window.addEventListener('resize', scaleFlyer);

});
</script>

</body>
</html>