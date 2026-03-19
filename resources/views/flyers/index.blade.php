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
                    YOURE ON THE FLYERS INDEX PAGE
                </div>
                <div>
                    @include('flyers.s1pc')
                </div>
                <div>
                    @include('flyers.s2pb')
                </div>
                <div>
                    @include('flyers.s3pt')
                </div>
                <div>
                    @include('flyers.s4sp')
                </div>
                <div>
                    @include('flyers.s5pt')
                </div>
            </div>
        </div>
    </div>
  </main>
  @include('public.layout.footer')
</body>
</html>



