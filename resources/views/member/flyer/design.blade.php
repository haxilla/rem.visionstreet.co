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

    // The flyer template partials (flyers.s1pc etc.) and the legacy
    // colorswatch/headline scripts expect a `$propInfo` variable shaped
    // exactly like the one app/flyers/index.php builds.
    $propInfo = $flyer;
    include(app_path() . '/flyers/variables.php');

    $initialTemplate = strtolower($flyer->theStyle->template ?: '1pc');

    $graphicWordsLabels = [
        'acreage' => 'Acreage', 'agentbonus' => 'Agent Bonus', 'amazingviews' => 'Amazing Views',
        'backonmarket' => 'Back On Market', 'bankowned' => 'Bank Owned', 'greatbuy' => 'Great Buy',
        'horseproperty' => 'Horse Property', 'justlisted' => 'Just Listed',
        'modelcloseout' => 'Model Closeout', 'mustsee' => 'Must See',
        'openhouse' => 'Open House', 'reduced' => 'Reduced',
    ];
    $initialHeadlineLabel = $graphicWordsLabels[$flyer->theStyle->graphic_words] ?? 'Great Buy';
@endphp

<main class="min-h-screen bg-[#f0f2f7] pt-24">

<div class="mx-auto flex w-full max-w-[1400px] gap-8 px-4 pb-16 sm:px-6 lg:px-8">

    <section class="min-w-0 flex-1">

        {{-- HEADER --}}
        <div class="mb-8">

            <div class="text-sm font-bold uppercase tracking-wider text-[#123f91]">
                Step 4 of 5
            </div>

            <h1 class="mt-2 text-4xl font-black text-slate-900">
                Design Your Flyer
            </h1>

            <p class="mt-2 text-slate-500">
                Pick a layout, colors, and headline banner.
            </p>

        </div>

        {{-- PROGRESS --}}
        @include('member.flyer.wizard', [
            'flyer' => $flyer
        ])

        {{-- PROPERTY SUMMARY --}}
        <div class="mb-8 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-black/5">

            <div class="text-xl font-black text-slate-900">
                {{ $flyer->xFullStreet }}
            </div>

            <div class="text-slate-600">
                {{ $flyer->xCity }},
                {{ $flyer->xState }}
                {{ $flyer->xZip }}
            </div>

        </div>

        @if($errors->any())

            <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 p-5">

                <div class="font-bold text-red-700">
                    Please correct the following:
                </div>

                <ul class="mt-3 list-disc pl-5 text-red-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>

        @endif

        <form id="designForm" method="POST" action="/member/flyer/save_design">
            @csrf

            <input type="hidden" name="flyerId" value="{{ $flyer->id }}">
            <input type="hidden" name="return" value="{{ request('return') }}">
            <input type="hidden" id="field_template" name="template" value="{{ $initialTemplate }}">
            <input type="hidden" id="field_flyer_background" name="flyer_background" value="{{ $flyer->theStyle->flyer_background }}">
            <input type="hidden" id="field_accentbars" name="accentbars" value="{{ $flyer->theStyle->accentbars }}">
            <input type="hidden" id="field_headline_bar_bg" name="headline_bar_bg" value="{{ $flyer->theStyle->headline_bar_bg }}">
            <input type="hidden" id="field_headline_bar_text" name="headline_bar_text" value="{{ $flyer->theStyle->headline_bar_text }}">
            <input type="hidden" id="field_headline_text" name="headline_text" value="{{ $flyer->theStyle->headline_text }}">
            <input type="hidden" id="field_graphic_words" name="graphic_words" value="{{ $flyer->theStyle->graphic_words }}">
            <input type="hidden" id="field_graphic_style" name="graphic_style" value="{{ $flyer->theStyle->graphic_style }}">
            <input type="hidden" id="field_graphic_textcolor" name="graphic_textcolor" value="{{ $flyer->theStyle->graphic_textcolor }}">

            {{-- STYLE / COLOR / HEADLINE CONTROLS - three progressive step
                 cards instead of tabs, so all three are always visible and
                 impossible to miss. Each locks until the one before it is
                 chosen; once chosen a step collapses to a summary that can
                 be reopened with "Edit". --}}
            <div class="mb-3 text-xs uppercase tracking-wide text-slate-400 px-1">
                Flyer Controls
            </div>

            <div class="mb-8 flex flex-col gap-3">

                {{-- STEP: STYLE --}}
                <div class="step-card" id="step-style" data-step="style">

                    <button type="button" class="step-header" data-step-toggle="style">
                        <span class="step-badge" id="style-badge">1</span>
                        <span class="step-text">
                            <span class="step-title">Style</span>
                            <span class="step-summary" id="style-summary">Style {{ substr($initialTemplate, 0, 1) }}</span>
                        </span>
                        <span class="step-edit">Edit</span>
                    </button>

                    <div class="step-body" id="style-body">

                        <p class="mb-3 text-xs text-slate-500">
                            Select a flyer layout
                        </p>

                        <div class="inline-flex overflow-hidden rounded-lg border border-slate-200">

                            <button type="button" class="flyer-btn border-r border-slate-200" data-target="s1pc">
                                Style 1
                            </button>

                            <button type="button" class="flyer-btn border-r border-slate-200" data-target="s2pb">
                                Style 2
                            </button>

                            <button type="button" class="flyer-btn border-r border-slate-200" data-target="s3pt">
                                Style 3
                            </button>

                            <button type="button" class="flyer-btn border-r border-slate-200" data-target="s4sp">
                                Style 4
                            </button>

                            <button type="button" class="flyer-btn" data-target="s5pt">
                                Style 5
                            </button>

                        </div>

                        <div class="mt-4">
                            <button type="button" class="step-confirm" data-confirm="style">
                                Choose this style
                            </button>
                        </div>

                    </div>

                </div>

                {{-- STEP: COLORS --}}
                <div class="step-card" id="step-colors" data-step="colors">

                    <button type="button" class="step-header" data-step-toggle="colors">
                        <span class="step-badge" id="colors-badge">2</span>
                        <span class="step-text">
                            <span class="step-title">Colors</span>
                            <span class="step-summary" id="colors-summary">Colors selected</span>
                        </span>
                        <span class="step-edit">Edit</span>
                    </button>

                    <div class="step-body" id="colors-body">

                        <div id="edit-colors">

                            <p class="mb-2 text-xs font-bold text-slate-500">Background</p>

                            <div class="mb-4 flex flex-wrap gap-1">

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#eeeeee;" data-style="background" data-scheme="light" data-color="eeeeee"></a>

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#cccccc;" data-style="background" data-scheme="light" data-color="cccccc"></a>

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#999999;" data-style="background" data-scheme="dark" data-color="999999"></a>

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#000066;" data-style="background" data-scheme="dark" data-color="000066"></a>

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#996600;" data-style="background" data-scheme="light" data-color="996600"></a>

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#990000;" data-style="background" data-scheme="dark" data-color="990000"></a>

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#000000;" data-style="background" data-scheme="dark" data-color="000000"></a>

                            </div>

                            <p class="mb-2 text-xs font-bold text-slate-500">Accents</p>

                            <p class="light-accents mb-1 text-xs text-slate-400">Light</p>

                            <div class="light-accents mb-3 flex flex-wrap gap-1">

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#ffffff;" data-style="accent" data-scheme="light" data-color="ffffff"></a>

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#eeeeee;" data-style="accent" data-scheme="light" data-color="eeeeee"></a>

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#ffffcc;" data-style="accent" data-scheme="light" data-color="ffffcc"></a>

                            </div>

                            <p class="dark-accents mb-1 text-xs text-slate-400">Dark</p>

                            <div class="dark-accents flex flex-wrap gap-1">

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#ffc60b;" data-style="accent" data-scheme="dark" data-color="ffc60b"></a>

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#990000;" data-style="accent" data-scheme="dark" data-color="990000"></a>

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#000066;" data-style="accent" data-scheme="dark" data-color="000066"></a>

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#00aeef;" data-style="accent" data-scheme="dark" data-color="00aeef"></a>

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#60b67b;" data-style="accent" data-scheme="dark" data-color="60b67b"></a>

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#f0535b;" data-style="accent" data-scheme="dark" data-color="f0535b"></a>

                                <a href="#" class="colorswatch block h-6 w-6 rounded border border-slate-300 transition-transform hover:scale-110"
                                style="background:#ff0000;" data-style="accent" data-scheme="dark" data-color="ff0000"></a>

                            </div>

                        </div>

                        <div class="mt-4">
                            <button type="button" class="step-confirm" data-confirm="colors">
                                Choose these colors
                            </button>
                        </div>

                    </div>

                </div>

                {{-- STEP: HEADLINE --}}
                <div class="step-card" id="step-headline" data-step="headline">

                    <button type="button" class="step-header" data-step-toggle="headline">
                        <span class="step-badge" id="headline-badge">3</span>
                        <span class="step-text">
                            <span class="step-title">Headline</span>
                            <span class="step-summary" id="headline-summary">{{ $initialHeadlineLabel }}</span>
                        </span>
                        <span class="step-edit">Edit</span>
                    </button>

                    <div class="step-body" id="headline-body">

                        <p class="mb-3 text-xs text-slate-500">
                            Select a headline
                        </p>

                        <div class="flex flex-wrap gap-3">

                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-500">
                                    Headline
                                </label>

                                <select id="headlineSelect"
                                    class="w-44 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#123f91]">
                                    <option value="acreage" @selected($flyer->theStyle->graphic_words === 'acreage')>Acreage</option>
                                    <option value="agentbonus" @selected($flyer->theStyle->graphic_words === 'agentbonus')>Agent Bonus</option>
                                    <option value="amazingviews" @selected($flyer->theStyle->graphic_words === 'amazingviews')>Amazing Views</option>
                                    <option value="backonmarket" @selected($flyer->theStyle->graphic_words === 'backonmarket')>Back On Market</option>
                                    <option value="bankowned" @selected($flyer->theStyle->graphic_words === 'bankowned')>Bank Owned</option>
                                    <option value="greatbuy" @selected($flyer->theStyle->graphic_words === 'greatbuy' || !$flyer->theStyle->graphic_words)>Great Buy</option>
                                    <option value="horseproperty" @selected($flyer->theStyle->graphic_words === 'horseproperty')>Horse Property</option>
                                    <option value="justlisted" @selected($flyer->theStyle->graphic_words === 'justlisted')>Just Listed</option>
                                    <option value="modelcloseout" @selected($flyer->theStyle->graphic_words === 'modelcloseout')>Model Closeout</option>
                                    <option value="mustsee" @selected($flyer->theStyle->graphic_words === 'mustsee')>Must See</option>
                                    <option value="openhouse" @selected($flyer->theStyle->graphic_words === 'openhouse')>Open House</option>
                                    <option value="reduced" @selected($flyer->theStyle->graphic_words === 'reduced')>Reduced</option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-bold text-slate-500">
                                    Style
                                </label>

                                <select id="headlineStyle"
                                    class="w-36 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#123f91]">
                                    <option value="bold" @selected($flyer->theStyle->graphic_style === 'bold')>Bold</option>
                                    <option value="3d" @selected($flyer->theStyle->graphic_style === '3d')>3D</option>
                                    <option value="ul" @selected($flyer->theStyle->graphic_style === 'ul' || !$flyer->theStyle->graphic_style)>Underline</option>
                                </select>
                            </div>

                        </div>

                        <div class="mt-4">
                            <button type="button" class="step-confirm" data-confirm="headline">
                                Choose this headline
                            </button>
                        </div>

                    </div>

                </div>

            </div>

            {{-- FLYER PREVIEW --}}
            <div class="mb-8 rounded-3xl bg-white p-4 shadow-sm ring-1 ring-black/5">

                <div class="flyer-stage">

                    <div id="flyer-scale-wrapper">

                        <div id="flyer-s1pc" class="flyer-panel">@include('flyers.s1pc')</div>
                        <div id="flyer-s2pb" class="flyer-panel">@include('flyers.s2pb')</div>
                        <div id="flyer-s3pt" class="flyer-panel">@include('flyers.s3pt')</div>
                        <div id="flyer-s4sp" class="flyer-panel">@include('flyers.s4sp')</div>
                        <div id="flyer-s5pt" class="flyer-panel">@include('flyers.s5pt')</div>

                    </div>

                </div>

            </div>

            <div class="mb-8 flex flex-col items-end gap-2">
                <button type="submit"
                    id="saveDesignBtn"
                    @disabled(!$flyer->theStyle->headline_chosen)
                    class="rounded-xl bg-[#123f91] px-6 py-3 font-bold text-white transition hover:bg-[#0f3274] disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500 disabled:hover:bg-slate-300">
                    Save & Continue →
                </button>
                <p id="save-hint" class="text-xs font-semibold text-slate-500"></p>
            </div>

        </form>

    </section>

</div>

</main>

@include('public.layout.footer')

<style>
    .flyer-panel { display: none; }
    .flyer-panel.active { display: block; }
    .flyer-btn { padding: 8px 16px; border: none; cursor: pointer; font-size: 14px; background: #f1f5f9; color: #334155; font-weight: 700; }
    .flyer-btn.active { background: #123f91; color: white; }
    .flyer-btn:not(.active):hover { background: #e2e8f0; }
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
    .step-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 1px 2px rgba(0,0,0,.06);
        border: 1px solid rgba(0,0,0,.05);
        overflow: hidden;
    }
    .step-card.locked { opacity: .5; }
    .step-header {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        background: none;
        border: none;
        text-align: left;
        cursor: pointer;
    }
    .step-card.locked .step-header { cursor: not-allowed; }
    .step-card.active .step-header { cursor: default; }
    .step-badge {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 13px;
        background: #e2e8f0;
        color: #64748b;
    }
    .step-card.active .step-badge { background: #123f91; color: #ffffff; }
    .step-card.complete .step-badge { background: #16a34a; color: #ffffff; }
    .step-text { flex: 1; min-width: 0; }
    .step-title { display: block; font-weight: 800; color: #0f172a; font-size: 15px; }
    .step-summary { display: none; font-size: 13px; color: #64748b; }
    .step-card.complete .step-summary { display: block; }
    .step-edit {
        display: none;
        flex-shrink: 0;
        font-size: 13px;
        font-weight: 700;
        color: #123f91;
    }
    .step-card.complete .step-edit { display: inline; }
    .step-body {
        padding: 0 20px 20px;
        border-top: 1px solid #f1f5f9;
        padding-top: 16px;
    }
    .step-body.hidden { display: none; }
    .step-confirm {
        border: none;
        border-radius: 10px;
        padding: 10px 18px;
        font-size: 13px;
        font-weight: 700;
        color: #ffffff;
        background: #123f91;
        cursor: pointer;
    }
    .step-confirm:hover { background: #0f3274; }
</style>

<script src="/my/js/flyers/colorswatch.js"></script>
<script src="/my/js/flyers/headline.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // ------------------------------------------------------------
    // Progressive unlock, shown as three stacked step-cards instead
    // of tabs so all three are always visible. The first not-yet-
    // chosen step is expanded and active; everything after it is
    // locked; everything before it collapses to a summary with an
    // "Edit" link. Once all three have ever been chosen (tracked
    // server-side via template_chosen/colors_chosen/headline_chosen),
    // every card stays freely reopenable and Save unlocks.
    // ------------------------------------------------------------

    const STEPS = ['style', 'colors', 'headline'];

    const chosen = {
        style:    {{ $flyer->theStyle->template_chosen ? 'true' : 'false' }},
        colors:   {{ $flyer->theStyle->colors_chosen ? 'true' : 'false' }},
        headline: {{ $flyer->theStyle->headline_chosen ? 'true' : 'false' }},
    };

    const saveBtn  = document.getElementById('saveDesignBtn');
    const saveHint = document.getElementById('save-hint');

    function renderSteps() {
        let activeAssigned = false;

        STEPS.forEach((step, i) => {
            const card  = document.getElementById('step-' + step);
            const body  = document.getElementById(step + '-body');
            const badge = document.getElementById(step + '-badge');

            card.classList.remove('locked', 'active', 'complete');

            if (chosen[step]) {
                card.classList.add('complete');
                badge.textContent = '✓';
            } else if (!activeAssigned) {
                card.classList.add('active');
                body.classList.remove('hidden');
                badge.textContent = String(i + 1);
                activeAssigned = true;
                return;
            } else {
                card.classList.add('locked');
                badge.textContent = String(i + 1);
            }

            body.classList.add('hidden');
        });

        saveBtn.disabled = !(chosen.style && chosen.colors && chosen.headline);
        saveHint.textContent = saveBtn.disabled
            ? 'Choose a style, colors, and a headline before saving.'
            : '';
    }

    renderSteps();

    function completeStep(step, summaryText) {
        chosen[step] = true;
        document.getElementById(step + '-summary').textContent = summaryText;
        renderSteps();
    }

    document.querySelectorAll('.step-header').forEach(header => {
        header.addEventListener('click', () => {
            const card = header.closest('.step-card');
            const step = card.dataset.step;

            if (card.classList.contains('locked')) return;

            if (card.classList.contains('complete')) {
                document.getElementById(step + '-body').classList.toggle('hidden');
            }
        });
    });

    // Clicking a style/color/headline option only updates the live
    // preview (via switchFlyer / colorswatch.js / headline.js below) -
    // it does NOT lock the step in. Browsing through several options
    // is fine; a step only becomes chosen when its confirm button is
    // clicked, using whatever's currently showing in the preview.

    document.querySelectorAll('[data-confirm]').forEach(confirmBtn => {
        confirmBtn.addEventListener('click', () => {
            const step = confirmBtn.dataset.confirm;

            if (step === 'style') {
                const activeBtn = document.querySelector('.flyer-btn.active');
                completeStep('style', activeBtn ? activeBtn.textContent.trim() : 'Style chosen');
            } else if (step === 'colors') {
                completeStep('colors', 'Colors selected');
            } else if (step === 'headline') {
                const headlineSelect = document.getElementById('headlineSelect');
                completeStep('headline', headlineSelect
                    ? headlineSelect.options[headlineSelect.selectedIndex].text
                    : 'Headline chosen');
            }
        });
    });

    function switchFlyer(target) {
        document.querySelectorAll('.flyer-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.flyer-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('flyer-' + target).classList.add('active');
        document.querySelector(`.flyer-btn[data-target="${target}"]`).classList.add('active');
        scaleFlyer();
    }

    document.querySelectorAll('.flyer-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            switchFlyer(btn.dataset.target);
        });
    });

    switchFlyer('s{{ $initialTemplate }}');

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

    // ------------------------------------------------------------
    // On submit, read the live-previewed choices back out of the DOM
    // (colorswatch.js / headline.js already keep it correct) into the
    // hidden fields that actually get saved. Anything that can't be
    // read falls back to the value already on the hidden input, so a
    // missing element never blanks out an existing saved value.
    // ------------------------------------------------------------

    function rgbToHex(rgbString) {
        const values = rgbString && rgbString.match(/\d+/g);
        if (!values) return null;

        return values
            .slice(0, 3)
            .map(v => Number(v).toString(16).padStart(2, '0'))
            .join('');
    }

    function bgHex(selector, root) {
        const el = (root || document).querySelector(selector);
        if (!el) return null;
        return rgbToHex(getComputedStyle(el).backgroundColor);
    }

    function textHex(selector, root) {
        const el = (root || document).querySelector(selector);
        if (!el) return null;
        return rgbToHex(getComputedStyle(el).color);
    }

    document.getElementById('designForm').addEventListener('submit', () => {

        // Read from the currently-selected style's own panel, not just
        // "the first element in the document with this class" - with 5
        // template partials in the DOM at once, that was silently
        // reading whichever template happened to render first (and one
        // of them, s1pc, doesn't even have .accent_bars/.headline_bar_bg/
        // .headline_bar_text at all), regardless of which style was
        // actually chosen.
        const activeFlyer = document.querySelector('.flyer-panel.active');

        const activeBtn = document.querySelector('.flyer-btn.active');
        if (activeBtn) {
            document.getElementById('field_template').value =
                activeBtn.dataset.target.replace(/^s/, '');
        }

        const background = bgHex('.flyer_background', activeFlyer);
        if (background) document.getElementById('field_flyer_background').value = background;

        const accent = bgHex('.accent_bars', activeFlyer);
        if (accent) document.getElementById('field_accentbars').value = accent;

        const headlineBarBg = bgHex('.headline_bar_bg', activeFlyer);
        if (headlineBarBg) document.getElementById('field_headline_bar_bg').value = headlineBarBg;

        const headlineBarText = textHex('.headline_bar_text', activeFlyer);
        if (headlineBarText) document.getElementById('field_headline_bar_text').value = headlineBarText;

        const headlineText = textHex('.headline_text', activeFlyer);
        if (headlineText) document.getElementById('field_headline_text').value = headlineText;

        const hlGraphic = activeFlyer ? activeFlyer.querySelector('.hlGraphic') : document.querySelector('.hlGraphic');
        if (hlGraphic) {
            const wordsMatch = hlGraphic.src.match(/headline_graphics\/([^/]+)\//);
            if (wordsMatch) document.getElementById('field_graphic_words').value = wordsMatch[1];

            const colorMatch = hlGraphic.src.match(/_([0-9a-fA-F]{6})_/);
            if (colorMatch) document.getElementById('field_graphic_textcolor').value = colorMatch[1];
        }

        const headlineStyleSelect = document.getElementById('headlineStyle');
        if (headlineStyleSelect) {
            document.getElementById('field_graphic_style').value = headlineStyleSelect.value;
        }

    });

});
</script>

</body>
</html>
