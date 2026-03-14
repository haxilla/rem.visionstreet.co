@include('public.layout.head')

<body data-section="admin" 
class="linkcheck relative bg-white min-h-screen font-sans text-gray-800 postgres">

  @include('public.layout.nav')

  <main class="transition-all duration-300 min-h-screen pt-24 relative"
  :class="collapsed ? 'ml-20' : 'ml-64'">
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
      <div class="pageswap p-6 w-full">
        @include('public.includes.pricing_content')
      </div>
    </div>
  </main>
  @include('public.layout.footer')
</body>
</html>