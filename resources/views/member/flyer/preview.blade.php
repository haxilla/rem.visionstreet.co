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

    $propInfo = $flyer;
    include(app_path() . '/flyers/variables.php');

    $templateView = 'flyers.s' . strtolower($flyer->theStyle->template ?: '1pc');
@endphp

<main class="min-h-screen bg-[#f0f2f7] pt-24">

<div class="mx-auto flex w-full max-w-[1400px] gap-8 px-4 pb-16 sm:px-6 lg:px-8">

    <section class="min-w-0 flex-1">

        {{-- HEADER --}}
        <div class="mb-8">

            <div class="text-sm font-bold uppercase tracking-wider text-[#123f91]">
                Step 5 of 5
            </div>

            <h1 class="mt-2 text-4xl font-black text-slate-900">
                Preview & Send
            </h1>

            <p class="mt-2 text-slate-500">
                This is exactly what recipients will see. Ready to send it out?
            </p>

        </div>

        {{-- PROGRESS --}}
        @include('member.flyer.wizard', [
            'flyer' => $flyer
        ])

        {{-- SEND NOW --}}
        <div class="mb-8 flex flex-col items-center justify-between gap-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5 sm:flex-row">

            <div>
                <div class="text-xl font-black text-slate-900">
                    Ready to send?
                </div>
                <div class="mt-1 text-sm text-slate-500">
                    Choose who receives this flyer and finish the email details.
                </div>
            </div>

            <a href="/member/flyer/sendsetup?flyerId={{ $flyer->id }}"
                class="shrink-0 rounded-xl bg-emerald-600 px-8 py-4 text-lg font-black text-white hover:bg-emerald-700">
                Send Now →
            </a>

        </div>

        {{-- FLYER PREVIEW --}}
        <div class="mb-8 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-black/5">

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

        <div class="mb-8 flex justify-between">

            <a href="/member/flyer/design?flyerId={{ $flyer->id }}"
                class="rounded-xl bg-white px-5 py-3 font-bold text-slate-700 shadow-sm ring-1 ring-black/5">
                ← Back to Design
            </a>

        </div>

    </section>

</div>

</main>

@include('public.layout.footer')

<style>
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
    .flyer-panel.active {
        display: block;
    }
</style>

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

});
</script>

</body>
</html>
