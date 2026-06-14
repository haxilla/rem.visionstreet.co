@include('public.layout.flyerhead')

<body data-section="admin" 
class="linkcheck relative bg-white min-h-screen font-sans text-gray-800 postgres">

  @include('member.layout.nav')

  <main class="transition-all duration-300 min-h-screen pt-24 relative"
  :class="collapsed ? 'ml-20' : 'ml-64'">
    <div class="mx-3 lg:mx-10">
        <div class="pageswap p-0 lg:p-6 w-full">
            @php 
                include(app_path().'/flyers/variables.php');
            @endphp

            <div class="max-w-[600px] mx-auto mb-3 flex items-center justify-between">

                <a href="/member"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900">
                    ← Back to Dashboard
                </a>

                <button type="button"
                class="inline-flex items-center gap-2 rounded-lg bg-[#1b2f63] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#223a75]">

                    <svg xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.75"
                    stroke="currentColor"
                    class="h-4 w-4">
                        <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5H4.5A2.25 2.25 0 0 0 2.25 6.75m19.5 0-8.69 5.516a2.25 2.25 0 0 1-2.12 0L2.25 6.75" />
                    </svg>

                    Save & Continue

                </button>

            </div>

            <div class="mb-4 border border-gray-200 rounded-xl bg-white shadow-sm overflow-hidden mx-auto"
            style="max-width:600px;">
                {{-- Header --}}
                <div class="px-4 pt-4 bg-gray-50 border-b border-gray-200">

                    <div class="text-xs uppercase tracking-wide text-gray-400 mb-3">
                        Flyer Controls
                    </div>

                    <div class="flex gap-1">

                        <button
                            class="control-tab active px-4 py-2 text-sm font-medium"
                            data-panel="styles-panel">
                            Style
                        </button>

                        <button
                            class="control-tab px-4 py-2 text-sm font-medium"
                            data-panel="colors-panel">
                            Colors
                        </button>

                        <button
                            class="control-tab px-4 py-2 text-sm font-medium"
                            data-panel="headline-panel">
                            Headline
                        </button>

                    </div>

                </div>

                {{-- Content --}}
                <div class="p-4">

                    <div id="styles-panel">

                        <p class="text-xs text-gray-500 mb-3">
                            Select a flyer layout
                        </p>

                        <div class="inline-flex rounded-lg overflow-hidden border border-gray-200">

                            <button class="flyer-btn border-r border-gray-200" data-target="s1pc">
                                Style 1
                            </button>

                            <button class="flyer-btn border-r border-gray-200" data-target="s2pb">
                                Style 2
                            </button>

                            <button class="flyer-btn border-r border-gray-200" data-target="s3pt">
                                Style 3
                            </button>

                            <button class="flyer-btn border-r border-gray-200" data-target="s4sp">
                                Style 4
                            </button>

                            <button class="flyer-btn" data-target="s5pt">
                                Style 5
                            </button>

                        </div>

                    </div>

                    <div id="colors-panel" class="hidden">

                        <div id="edit-colors">

                            <p class="text-xs font-medium text-gray-500 mb-2">Background</p>

                            <div class="flex flex-wrap gap-1 mb-4">

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#eeeeee;" data-style="background" data-scheme="light" data-color="eeeeee"></a>

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#cccccc;" data-style="background" data-scheme="light" data-color="cccccc"></a>

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#999999;" data-style="background" data-scheme="dark" data-color="999999"></a>

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#000066;" data-style="background" data-scheme="dark" data-color="000066"></a>

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#996600;" data-style="background" data-scheme="light" data-color="996600"></a>

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#990000;" data-style="background" data-scheme="dark" data-color="990000"></a>

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#000000;" data-style="background" data-scheme="dark" data-color="000000"></a>

                            </div>

                            <p class="text-xs font-medium text-gray-500 mb-2">Accents</p>

                            <p class="light-accents text-xs text-gray-400 mb-1">Light</p>

                            <div class="light-accents flex flex-wrap gap-1 mb-3">

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#ffffff;" data-style="accent" data-scheme="light" data-color="ffffff"></a>

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#eeeeee;" data-style="accent" data-scheme="light" data-color="eeeeee"></a>

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#ffffcc;" data-style="accent" data-scheme="light" data-color="ffffcc"></a>

                            </div>

                            <p class="dark-accents text-xs text-gray-400 mb-1">Dark</p>

                            <div class="dark-accents flex flex-wrap gap-1">

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#ffc60b;" data-style="accent" data-scheme="dark" data-color="ffc60b"></a>

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#990000;" data-style="accent" data-scheme="dark" data-color="990000"></a>

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#000066;" data-style="accent" data-scheme="dark" data-color="000066"></a>

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#00aeef;" data-style="accent" data-scheme="dark" data-color="00aeef"></a>

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#60b67b;" data-style="accent" data-scheme="dark" data-color="60b67b"></a>

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#f0535b;" data-style="accent" data-scheme="dark" data-color="f0535b"></a>

                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform"
                                style="background:#ff0000;" data-style="accent" data-scheme="dark" data-color="ff0000"></a>

                            </div>

                        </div>

                    </div>

                    <div id="headline-panel" class="hidden">

                        <p class="text-xs text-gray-500 mb-3">
                            Select a headline
                        </p>

                        <div class="flex flex-wrap gap-3">

                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">
                                    Headline
                                </label>

                                <select id="headlineSelect"
                                    class="w-44 border border-gray-300 rounded-lg bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Select headline --</option>
                                    <option value="acreage">Acreage</option>
                                    <option value="agentbonus">Agent Bonus</option>
                                    <option value="amazingviews">Amazing Views</option>
                                    <option value="backonmarket">Back On Market</option>
                                    <option value="bankowned">Bank Owned</option>
                                    <option value="greatbuy">Great Buy</option>
                                    <option value="horseproperty">Horse Property</option>
                                    <option value="justlisted">Just Listed</option>
                                    <option value="modelcloseout">Model Closeout</option>
                                    <option value="mustsee">Must See</option>
                                    <option value="openhouse">Open House</option>
                                    <option value="reduced">Reduced</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">
                                    Style
                                </label>

                                <select id="headlineStyle"
                                    class="w-36 border border-gray-300 rounded-lg bg-white px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">-- Select style --</option>
                                    <option value="bold">Bold</option>
                                    <option value="3d">3D</option>
                                    <option value="ul">Underline</option>
                                </select>
                            </div>

                        </div>

                    </div>                    

                </div>

            </div>

            {{-- Main editor layout --}}
            <div class="flex flex-col xl:flex-row gap-6 items-start">

                {{-- Left: Flyer preview --}}
                <div class="flex-1 min-w-0 flyer-stage">

                    <div id="flyer-scale-wrapper">

                        <div id="flyer-s1pc" class="flyer-panel">@include('flyers.s1pc')</div>
                        <div id="flyer-s2pb" class="flyer-panel">@include('flyers.s2pb')</div>
                        <div id="flyer-s3pt" class="flyer-panel">@include('flyers.s3pt')</div>
                        <div id="flyer-s4sp" class="flyer-panel">@include('flyers.s4sp')</div>
                        <div id="flyer-s5pt" class="flyer-panel">@include('flyers.s5pt')</div>

                    </div>

                </div>

            </div>


            <style>
                .flyer-panel { display: none; }
                .flyer-panel.active { display: block; }
                .flyer-btn { padding: 4px 12px; border-radius: 0; border: none; cursor: pointer; font-size: 14px; background: #e5e7eb; color: #374151; }
                .flyer-btn.active { background: #2563eb; color: white; }
                .flyer-btn:not(.active):hover {background: #f3f4f6;}                
                .flyer-stage {
                    width:100%;
                    overflow:hidden;
                    filter:drop-shadow(0 10px 25px rgba(0,0,0,.12));
                }
                #flyer-scale-wrapper {
                    width:600px;
                    transform-origin:top left;
                    margin:0 auto;
                }               
                
                .control-tab{
                    color:#6b7280;
                    border:1px solid transparent;
                    border-bottom:none;
                    border-radius:8px 8px 0 0;
                }

                .control-tab.active{
                    background:#ffffff;
                    color:#111827;
                    border:1px solid #d1d5db;
                    border-bottom:1px solid #ffffff;
                }
                body{
                    background-color:#e6ebf3;

                    background-image:
                        linear-gradient(
                            135deg,
                            rgba(255,255,255,.35) 25%,
                            transparent 25%
                        ),
                        linear-gradient(
                            315deg,
                            rgba(255,255,255,.20) 25%,
                            transparent 25%
                        );

                    background-size:600px 600px;
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', () => {

                    // Flyer switcher
                    function switchFlyer(target) {
                        document.querySelectorAll('.flyer-panel').forEach(p => p.classList.remove('active'));
                        document.querySelectorAll('.flyer-btn').forEach(b => b.classList.remove('active'));
                        document.getElementById('flyer-' + target).classList.add('active');
                        document.querySelector(`.flyer-btn[data-target="${target}"]`).classList.add('active');
                    }

                    document.querySelectorAll('.flyer-btn').forEach(btn => {
                        btn.addEventListener('click', () => {

                            switchFlyer(btn.dataset.target);

                            requestAnimationFrame(() => {
                                scaleFlyer();
                            });

                        });
                    });

                    switchFlyer('s{{ $template }}');

                    document.querySelectorAll('.control-tab').forEach(tab => {

                        tab.addEventListener('click', () => {

                            document.querySelectorAll('.control-tab')
                                .forEach(t => t.classList.remove('active'));

                            tab.classList.add('active');

                            document.getElementById('styles-panel')
                                .classList.add('hidden');

                            document.getElementById('colors-panel')
                                .classList.add('hidden');
                            
                            document.getElementById('headline-panel')
                                .classList.add('hidden');

                            document.getElementById(tab.dataset.panel)
                                .classList.remove('hidden');

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

                        console.log('availableWidth=', availableWidth);
                        console.log('scale=', scale);

                        wrapper.style.transformOrigin = 'top left';
                        wrapper.style.transform = `scale(${scale})`;

                        wrapper.style.height =
                            (activeFlyer.offsetHeight * scale) + 'px';

                    }

                    scaleFlyer();
                    window.addEventListener('resize', scaleFlyer);

                });
            </script>
        </div>
    </main>
    <script src="/my/js/flyers/colorswatch.js"></script>  
    <script src="/my/js/flyers/headline.js"></script>

    @include('public.layout.footer')
    </body>
</html>

<!---

                {{-- Right: Editor panel --}}
                <div class="w-72 shrink-0 border border-gray-200 rounded-xl shadow-sm bg-white sticky top-28">

                    {{-- Editor tabs --}}
                    <div class="flex border-b border-gray-200">
                        <button class="editor-tab flex-1 py-2 text-xs font-medium text-gray-500 hover:text-gray-800 border-b-2 border-transparent hover:border-gray-300 transition-colors" data-panel="edit-headline">
                            Headline
                        </button>
                        <button class="editor-tab flex-1 py-2 text-xs font-medium text-gray-500 hover:text-gray-800 border-b-2 border-transparent hover:border-gray-300 transition-colors" data-panel="edit-text">
                            Text
                        </button>
                        <button class="editor-tab flex-1 py-2 text-xs font-medium text-gray-500 hover:text-gray-800 border-b-2 border-transparent hover:border-gray-300 transition-colors" data-panel="edit-photos">
                            Photos
                        </button>
                        <button class="editor-tab flex-1 py-2 text-xs font-medium text-gray-500 hover:text-gray-800 border-b-2 border-transparent hover:border-gray-300 transition-colors" data-panel="edit-colors">
                            Colors
                        </button>
                    </div>

                    {{-- Editor panels --}}
                    <div class="p-4">

                        <div id="edit-headline" class="editor-panel">
                            <select id="headlineSelect">
                                <option value="">-- Select headline --</option>
                                <option value="acreage">Acreage</option>
                                <option value="agentbonus">Agent Bonus</option>
                                <option value="amazingviews">Amazing Views</option>
                                <option value="backonmarket">Back On Market</option>
                                <option value="bankowned">Bank Owned</option>
                                <option value="greatbuy">Great Buy</option>
                                <option value="horseproperty">Horse Property</option>
                                <option value="justlisted">Just Listed</option>
                                <option value="modelcloseout">Model Closeout</option>
                                <option value="mustsee">Must See</option>
                                <option value="openhouse">Open House</option>
                                <option value="reduced">Reduced</option>
                            </select>
                            <select id="headlineStyle">
                                <option value="">-- Select Style --</option>
                                <option value="bold">Bold</option>
                                <option value="3d">3D</option>
                                <option value="ul">Underline</option>
                            </select>
                        </div>

                        <div id="edit-text" class="editor-panel hidden">
                            <p class="text-xs text-gray-500 mb-2">Body text</p>
                            <textarea rows="5" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none" placeholder="Enter body text..."></textarea>
                            <button class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded-lg transition-colors">Apply</button>
                        </div>

                        <div id="edit-photos" class="editor-panel hidden">
                            <p class="text-xs text-gray-500 mb-2">Upload photo</p>
                            <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors">
                                <svg class="w-6 h-6 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span class="text-xs text-gray-400">Click to upload</span>
                                <input type="file" class="hidden" accept="image/*">
                            </label>
                            <button class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded-lg transition-colors">Apply</button>
                        </div>

                        {{-- THIS IS THE ONLY edit-colors div --}}
                        <div id="edit-colors" class="editor-panel hidden">

                            <p class="text-xs font-medium text-gray-500 mb-2">Background</p>
                            <div class="flex flex-wrap gap-1 mb-4">
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#eeeeee;" data-style="background" data-scheme="light" data-color="eeeeee"></a>
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#cccccc;" data-style="background" data-scheme="light" data-color="cccccc"></a>
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#999999;" data-style="background" data-scheme="dark" data-color="999999"></a>
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#000066;" data-style="background" data-scheme="dark" data-color="000066"></a>
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#996600;" data-style="background" data-scheme="light" data-color="996600"></a>
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#990000;" data-style="background" data-scheme="dark" data-color="990000"></a>
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#000000;" data-style="background" data-scheme="dark" data-color="000000"></a>
                            </div>

                            <p class="text-xs font-medium text-gray-500 mb-2">Accents</p>

                            <p class="light-accents text-xs text-gray-400 mb-1">Light</p>
                            <div class="light-accents flex flex-wrap gap-1 mb-3">
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#ffffff;" data-style="accent" data-scheme="light" data-color="ffffff"></a>
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#eeeeee;" data-style="accent" data-scheme="light" data-color="eeeeee"></a>
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#ffffcc;" data-style="accent" data-scheme="light" data-color="ffffcc"></a>
                            </div>

                            <p class="dark-accents text-xs text-gray-400 mb-1">Dark</p>
                            <div class="dark-accents flex flex-wrap gap-1">
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#ffc60b;" data-style="accent" data-scheme="dark" data-color="ffc60b"></a>
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#990000;" data-style="accent" data-scheme="dark" data-color="990000"></a>
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#000066;" data-style="accent" data-scheme="dark" data-color="000066"></a>
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#00aeef;" data-style="accent" data-scheme="dark" data-color="00aeef"></a>
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#60b67b;" data-style="accent" data-scheme="dark" data-color="60b67b"></a>
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#f0535b;" data-style="accent" data-scheme="dark" data-color="f0535b"></a>
                                <a href="#" class="colorswatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" 
                                style="background:#ff0000;" data-style="accent" data-scheme="dark" data-color="ff0000"></a>
                            </div>

                        </div>

                    </div>
                </div>

--->