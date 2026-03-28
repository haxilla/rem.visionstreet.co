@include('public.layout.head')

<body data-section="admin" 
class="linkcheck relative bg-white min-h-screen font-sans text-gray-800 postgres">

  @include('public.layout.nav')

  <main class="transition-all duration-300 min-h-screen pt-24 relative"
  :class="collapsed ? 'ml-20' : 'ml-64'">
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
        <div class="pageswap p-6 w-full">
            @php 
                include(app_path().'/flyers/variables.php');
            @endphp

            {{-- Template switcher --}}
            <div class="flex gap-2 mb-4 flex-wrap">
                <button class="flyer-btn" data-target="s1pc">S1PC</button>
                <button class="flyer-btn" data-target="s2pb">S2PB</button>
                <button class="flyer-btn" data-target="s3pt">S3PT</button>
                <button class="flyer-btn" data-target="s4sp">S4SP</button>
                <button class="flyer-btn" data-target="s5pt">S5PT</button>
            </div>

            {{-- Main editor layout --}}
            <div class="flex gap-6 items-start">

                {{-- Left: Flyer preview --}}
                <div class="flex-1 min-w-0" style="max-width: 600px;">
                    <div id="flyer-s1pc" class="flyer-panel">@include('flyers.s1pc')</div>
                    <div id="flyer-s2pb" class="flyer-panel">@include('flyers.s2pb')</div>
                    <div id="flyer-s3pt" class="flyer-panel">@include('flyers.s3pt')</div>
                    <div id="flyer-s4sp" class="flyer-panel">@include('flyers.s4sp')</div>
                    <div id="flyer-s5pt" class="flyer-panel">@include('flyers.s5pt')</div>
                </div>

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
                            <p class="text-xs text-gray-500 mb-2">Main headline</p>
                            <input type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter headline...">
                            <p class="text-xs text-gray-500 mt-3 mb-2">Sub headline</p>
                            <input type="text" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter sub headline...">
                            <button class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded-lg transition-colors">Apply</button>
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

                        <div id="edit-colors" class="editor-panel hidden">
                            <p class="text-xs text-gray-500 mb-2">Primary color</p>
                            <div class="flex items-center gap-2">
                                <input type="color" value="#2563eb" class="w-10 h-9 rounded border border-gray-200 cursor-pointer p-0.5">
                                <input type="text" value="#2563eb" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <p class="text-xs text-gray-500 mt-3 mb-2">Secondary color</p>
                            <div class="flex items-center gap-2">
                                <input type="color" value="#111827" class="w-10 h-9 rounded border border-gray-200 cursor-pointer p-0.5">
                                <input type="text" value="#111827" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <p class="text-xs text-gray-500 mt-3 mb-2">Background color</p>
                            <div class="flex items-center gap-2">
                                <input type="color" value="#ffffff" class="w-10 h-9 rounded border border-gray-200 cursor-pointer p-0.5">
                                <input type="text" value="#ffffff" class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <button class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded-lg transition-colors">Apply</button>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <style>
            .flyer-panel { display: none; }
            .flyer-panel.active { display: block; }
            .flyer-btn { padding: 4px 12px; border-radius: 4px; border: none; cursor: pointer; font-size: 14px; background: #e5e7eb; color: #374151; }
            .flyer-btn.active { background: #2563eb; color: white; }
            .editor-tab.active { color: #1d4ed8; border-bottom-color: #2563eb; }
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
                    btn.addEventListener('click', () => switchFlyer(btn.dataset.target));
                });

                switchFlyer('s{{ $template }}');

                // Editor tab switcher
                function switchEditorTab(panelId) {
                    document.querySelectorAll('.editor-panel').forEach(p => p.classList.add('hidden'));
                    document.querySelectorAll('.editor-tab').forEach(t => t.classList.remove('active'));
                    document.getElementById(panelId).classList.remove('hidden');
                    document.querySelector(`.editor-tab[data-panel="${panelId}"]`).classList.add('active');
                }

                document.querySelectorAll('.editor-tab').forEach(tab => {
                    tab.addEventListener('click', () => switchEditorTab(tab.dataset.panel));
                });

                // Activate first tab by default
                switchEditorTab('edit-headline');

                // Sync color picker <-> hex input
                document.querySelectorAll('#edit-colors input[type=color]').forEach(picker => {
                    const textInput = picker.nextElementSibling;
                    picker.addEventListener('input', () => textInput.value = picker.value);
                    textInput.addEventListener('input', () => {
                        if (/^#[0-9a-f]{6}$/i.test(textInput.value)) picker.value = textInput.value;
                    });
                });

            });
        </script>
    </div>

<div id="edit-colors" class="editor-panel hidden">

    {{-- Background swatches --}}
    <p class="text-xs font-medium text-gray-500 mb-2">Background</p>
    <div class="flex flex-wrap gap-1 mb-4">
        <a href="/mdbxMember/colorChoice/{{$propInfo->theMeta->sk1}}/b/eeeeee" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#eeeeee;" title="#eeeeee"></a>
        <a href="/mdbxMember/colorChoice/{{$propInfo->theMeta->sk1}}/b/cccccc" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#cccccc;" title="#cccccc"></a>
        @if($template == '1pc')
            <a href="/mdbxMember/colorChoice/{{$propInfo->theMeta->sk1}}/b/999999" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#999999;" title="#999999"></a>
        @endif
        <a href="/mdbxMember/colorChoice/{{$propInfo->theMeta->sk1}}/b/000066" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#000066;" title="#000066"></a>
        <a href="/mdbxMember/colorChoice/{{$propInfo->theMeta->sk1}}/b/996600" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#996600;" title="#996600"></a>
        <a href="/mdbxMember/colorChoice/{{$propInfo->theMeta->sk1}}/b/990000" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#990000;" title="#990000"></a>
        <a href="/mdbxMember/colorChoice/{{$propInfo->theMeta->sk1}}/b/000000" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#000000;" title="#000000"></a>
    </div>

    {{-- Accent swatches --}}
    <p class="text-xs font-medium text-gray-500 mb-2">Accents</p>

    {{-- Light accents --}}
    <p class="text-xs text-gray-400 mb-1">Light</p>
    <div class="flex flex-wrap gap-1 mb-3">
        <a href="#" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#ffffff;" title="#ffffff"></a>
        <a href="#" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#eeeeee;" title="#eeeeee"></a>
        <a href="#" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#ffffcc;" title="#ffffcc"></a>
    </div>

    {{-- Dark accents --}}
    <p class="text-xs text-gray-400 mb-1">Dark</p>
    <div class="flex flex-wrap gap-1 mb-4">
        <a href="#" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#ffc60b;" title="#ffc60b"></a>
        <a href="#" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#990000;" title="#990000"></a>
        <a href="#" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#000066;" title="#000066"></a>
        <a href="#" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#00aeef;" title="#00aeef"></a>
        <a href="/mdbxMember/colorChoice/{{$propInfo->theMeta->sk1}}/t/60b67b" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#60b67b;" title="#60b67b"></a>
        <a href="#" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#f0535b;" title="#f0535b"></a>
        <a href="#" class="color-swatch block w-6 h-6 rounded border border-gray-300 hover:scale-110 transition-transform" style="background:#ff0000;" title="#ff0000"></a>
    </div>

</div>

  </main>
  @include('public.layout.footer')
</body>
</html>