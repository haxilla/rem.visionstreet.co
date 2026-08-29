@include('member.layout.head')

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

        <form id="designForm" method="POST" action="/member/flyer/save_design">
            @csrf

            <input type="hidden" name="flyerId" value="{{ $flyer->id }}">
            <input type="hidden" id="field_template" name="template" value="{{ $initialTemplate }}">
            <input type="hidden" id="field_flyer_background" name="flyer_background" value="{{ $flyer->theStyle->flyer_background }}">
            <input type="hidden" id="field_accentbars" name="accentbars" value="{{ $flyer->theStyle->accentbars }}">
            <input type="hidden" id="field_headline_bar_bg" name="headline_bar_bg" value="{{ $flyer->theStyle->headline_bar_bg }}">
            <input type="hidden" id="field_headline_bar_text" name="headline_bar_text" value="{{ $flyer->theStyle->headline_bar_text }}">
            <input type="hidden" id="field_headline_text" name="headline_text" value="{{ $flyer->theStyle->headline_text }}">
            <input type="hidden" id="field_graphic_words" name="graphic_words" value="{{ $flyer->theStyle->graphic_words }}">
            <input type="hidden" id="field_graphic_style" name="graphic_style" value="{{ $flyer->theStyle->graphic_style }}">
            <input type="hidden" id="field_graphic_textcolor" name="graphic_textcolor" value="{{ $flyer->theStyle->graphic_textcolor }}">

            {{-- STYLE / COLOR / HEADLINE CONTROLS --}}
            <div class="mb-8 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-black/5">

                <div class="px-6 pt-6">

                    <div class="mb-3 text-xs uppercase tracking-wide text-slate-400">
                        Flyer Controls
                    </div>

                    <div class="flex gap-1">

                        <button type="button"
                            class="control-tab active px-4 py-2 text-sm font-bold"
                            data-panel="styles-panel">
                            Style
                        </button>

                        <button type="button"
                            class="control-tab px-4 py-2 text-sm font-bold"
                            data-panel="colors-panel">
                            Colors
                        </button>

                        <button type="button"
                            class="control-tab px-4 py-2 text-sm font-bold"
                            data-panel="headline-panel">
                            Headline
                        </button>

                    </div>

                </div>

                <div class="p-6">

                    <div id="styles-panel">

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

                    </div>

                    <div id="colors-panel" class="hidden">

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

                    </div>

                    <div id="headline-panel" class="hidden">

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

            <div class="mb-8 flex justify-end">
                <button type="submit"
                    class="rounded-xl bg-[#123f91] px-6 py-3 font-bold text-white hover:bg-[#0f3274]">
                    Save & Continue →
                </button>
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
    .control-tab {
        color: #64748b;
        border: 1px solid transparent;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
        font-weight: 700;
        cursor: pointer;
        background: none;
    }
    .control-tab.active {
        background: #ffffff;
        color: #111827;
        border: 1px solid #e2e8f0;
        border-bottom: 1px solid #ffffff;
    }
</style>

<script src="/my/js/flyers/colorswatch.js"></script>
<script src="/my/js/flyers/headline.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

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

    document.querySelectorAll('.control-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.control-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            document.getElementById('styles-panel').classList.add('hidden');
            document.getElementById('colors-panel').classList.add('hidden');
            document.getElementById('headline-panel').classList.add('hidden');

            document.getElementById(tab.dataset.panel).classList.remove('hidden');
        });
    });

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

    function bgHex(selector) {
        const el = document.querySelector(selector);
        if (!el) return null;
        return rgbToHex(getComputedStyle(el).backgroundColor);
    }

    function textHex(selector) {
        const el = document.querySelector(selector);
        if (!el) return null;
        return rgbToHex(getComputedStyle(el).color);
    }

    document.getElementById('designForm').addEventListener('submit', () => {

        const activeBtn = document.querySelector('.flyer-btn.active');
        if (activeBtn) {
            document.getElementById('field_template').value =
                activeBtn.dataset.target.replace(/^s/, '');
        }

        const background = bgHex('.flyer_background');
        if (background) document.getElementById('field_flyer_background').value = background;

        const accent = bgHex('.accent_bars');
        if (accent) document.getElementById('field_accentbars').value = accent;

        const headlineBarBg = bgHex('.headline_bar_bg');
        if (headlineBarBg) document.getElementById('field_headline_bar_bg').value = headlineBarBg;

        const headlineBarText = textHex('.headline_bar_text');
        if (headlineBarText) document.getElementById('field_headline_bar_text').value = headlineBarText;

        const headlineText = textHex('.headline_text');
        if (headlineText) document.getElementById('field_headline_text').value = headlineText;

        const hlGraphic = document.querySelector('.hlGraphic');
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
