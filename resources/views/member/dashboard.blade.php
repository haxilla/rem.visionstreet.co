@include('public.layout.head')

<body data-section="admin"
class="relative bg-white min-h-screen font-sans text-gray-800">

  @include('public.layout.nav')

  <main class="transition-all duration-300 min-h-screen pt-24 relative"
  :class="collapsed ? 'ml-20' : 'ml-64'">
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
      <div class="pageswap p-6 w-full">

        <h1 class="text-2xl font-semibold text-gray-800">Member Dashboard</h1>
        <p class="text-gray-500 mt-1">Welcome back, {{ Auth::guard('member')->user()->theAgent->agtFullName}}.</p>

      </div>
    </div>
  </main>

  @include('public.layout.footer')
</body>
</html>