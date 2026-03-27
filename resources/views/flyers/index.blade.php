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
            <div style="max-width: 600px; margin: 0 auto;">
                <div>
                    YOURE ON THE FLYERS INDEX PAGE {{ $template }}
                </div>

                <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;">
                    <button class="flyer-btn" data-target="s1pc">S1PC</button>
                    <button class="flyer-btn" data-target="s2pb">S2PB</button>
                    <button class="flyer-btn" data-target="s3pt">S3PT</button>
                    <button class="flyer-btn" data-target="s4sp">S4SP</button>
                    <button class="flyer-btn" data-target="s5pt">S5PT</button>
                </div>

                <div id="flyer-s1pc" class="flyer-panel">@include('flyers.s1pc')</div>
                <div id="flyer-s2pb" class="flyer-panel">@include('flyers.s2pb')</div>
                <div id="flyer-s3pt" class="flyer-panel">@include('flyers.s3pt')</div>
                <div id="flyer-s4sp" class="flyer-panel">@include('flyers.s4sp')</div>
                <div id="flyer-s5pt" class="flyer-panel">@include('flyers.s5pt')</div>

            </div>
        </div>

        <style>
            .flyer-panel { display: none; }
            .flyer-panel.active { display: block; }
            .flyer-btn { padding: 4px 12px; border-radius: 4px; border: none; cursor: pointer; font-size: 14px; background: #e5e7eb; color: #374151; }
            .flyer-btn.active { background: #2563eb; color: white; }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
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
            });
        </script>
    </div>
  </main>
  @include('public.layout.footer')
</body>
</html>



