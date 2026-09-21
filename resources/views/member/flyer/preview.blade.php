@include('member.layout.head')

{{-- The flyer template partials (flyers.s1pc etc.) rely on these
     legacy stylesheets for classes like .style1LeftBackground,
     .agentImage, .flyerIcons - member.layout.head doesn't include
     them, only public.layout.flyerhead does. --}}
<link rel="stylesheet" type="text/css" href="/my/css/flyers/styles1pc.css">
<link rel="stylesheet" type="text/css" href="/my/css/flyers/styles2pb.css">
<link rel="stylesheet" type="text/css" href="/my/css/flyers/styles3pt.css">
<link rel="stylesheet" type="text/css" href="/my/css/flyers/styles4sp.css">
<link rel="stylesheet" type="text/css" href="/my/css/flyers/styles5pt.css">
<link rel="stylesheet" type="text/css" href="/my/css/flyers/flyerPreviews.css">

<body data-section="member" class="relative min-h-screen bg-[#f0f2f7] font-sans text-slate-800">

@include('member.layout.nav')

@php
    $flyer = $data['flyer'] ?? null;
    $remCredits = $data['remCredits'] ?? 0;

    // Trial mode (admin Settings): no credits are needed or charged.
    $trialMode = $data['trialMode'] ?? false;
    $creditsBlocked = !$trialMode && $remCredits <= 0;

    $propInfo = $flyer;
    include(app_path() . '/flyers/variables.php');

    $templateView = 'flyers.s' . strtolower($flyer->theStyle->template ?: '1pc');
@endphp

<main class="min-h-screen bg-[#f0f2f7] pt-[88px]">

<div class="mx-auto w-full max-w-[1400px] px-4 pb-10 sm:px-6 lg:px-8">

    <section class="min-w-0">

        {{-- HEADER --}}
        <div class="mb-3 flex flex-wrap items-end justify-between gap-x-4 gap-y-2">

            <div>
                <div class="flex flex-wrap items-baseline gap-x-3 gap-y-0.5">
                    <h1 class="text-2xl font-black leading-tight text-slate-900">
                        Preview & Send
                    </h1>

                    <span class="text-xs font-bold uppercase tracking-wider text-[#123f91]">
                        Step 5 of 5
                    </span>
                </div>

                <p class="text-sm text-slate-500">
                    This is exactly what recipients will see. Ready to send it out?
                </p>
            </div>

            @if($creditsBlocked)
                <div class="flex items-center gap-3 rounded-lg bg-amber-50 px-3 py-2 text-amber-800 ring-1 ring-amber-200">
                    <div class="text-sm font-bold">
                        You need credits to send this flyer.
                    </div>
                    <a href="/member/buy-credits" class="shrink-0 rounded-md bg-amber-600 px-3 py-1.5 text-xs font-black text-white hover:bg-amber-700">
                        Buy Credits
                    </a>
                </div>
            @endif

        </div>

        {{-- PROGRESS --}}
        @include('member.flyer.wizard', [
            'flyer' => $flyer
        ])

        {{-- SEND NOW --}}
        <div class="wz-card mb-4 flex flex-col items-center justify-between gap-3 py-4 sm:flex-row">

            <div>
                <div class="text-base font-black text-slate-900">
                    Ready to send?
                </div>
                <div class="text-sm text-slate-500">
                    Choose who receives this flyer and finish the email details.
                </div>
            </div>

            @if($creditsBlocked)
                <a href="/member/buy-credits"
                    class="wz-btn shrink-0 bg-amber-600 text-white hover:bg-amber-700">
                    Purchase Credits
                </a>
            @else
                <a href="/member/flyer/sendsetup?flyerId={{ $flyer->id }}"
                    class="wz-btn shrink-0 bg-emerald-600 text-white hover:bg-emerald-700">
                    Finalize →
                </a>
            @endif

        </div>

        {{-- FLYER PREVIEW --}}
        <div class="wz-card mb-4 p-3">

            <div class="flyer-stage">

                <div id="flyer-scale-wrapper">

                    <div class="flyer-panel active">

                        @if(View::exists($templateView))
                            @include($templateView)
                        @else
                            <div class="p-10 text-center text-slate-500">
                                No template selected yet.
                            </div>
                        @endif

                    </div>

                </div>

            </div>

        </div>

        <div class="flex justify-between">

            <a href="/member/flyer/design?flyerId={{ $flyer->id }}"
                class="wz-btn wz-btn-secondary">
                ← Back to Design
            </a>

        </div>

    </section>

</div>

</main>

@include('member.flyer.flyerEditModals', ['flyer' => $flyer])

@include('public.layout.footer')

<style>
    /* .flyer-stage / #flyer-scale-wrapper live in resources/css/components/wizard.css */
    .flyer-panel.active {
        display: block;
    }
</style>

<script src="/my/js/flyers/photoSwap.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

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

    // The flyer's height changes after this first pass as its photos and logos
    // load (a longer flyer was clipped until a reload, when they were cached),
    // so measure again whenever the flyer or its column changes size.
    window.addEventListener('load', scaleFlyer);

    if ('ResizeObserver' in window) {
        const observer = new ResizeObserver(scaleFlyer);
        const stage = document.querySelector('.flyer-stage');

        document.querySelectorAll('#flyer-scale-wrapper .flyer-panel').forEach(panel => observer.observe(panel));
        if (stage) observer.observe(stage);
    }

});
</script>

</body>
</html>
