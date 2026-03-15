@include('public.layout.head')

<body data-section="admin"
class="linkcheck relative bg-white min-h-screen font-sans text-gray-800 postgres">

  @include('public.layout.nav')

  <main class="transition-all duration-300 min-h-screen pt-24 relative"
  :class="collapsed ? 'ml-20' : 'ml-64'">
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
      <div class="pageswap w-full">

        <div class="min-h-[calc(100vh-6rem)] flex">

          {{-- ── LEFT: Hero Image Panel ── --}}
          <div class="hidden lg:flex lg:w-3/5 relative overflow-hidden rounded-2xl my-8">

            {{-- Background image --}}
            <img
              src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1400&q=85&auto=format&fit=crop"
              alt="Luxury Property"
              class="absolute inset-0 w-full h-full object-cover"
            />

            {{-- Dark overlay --}}
            <div class="absolute inset-0 bg-gradient-to-b from-[#1a2235]/70 via-[#1a2235]/40 to-[#1a2235]/80"></div>

            {{-- Content --}}
            <div class="relative z-10 flex flex-col justify-between h-full p-10 text-white">

              {{-- Top: brand --}}
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-white/10 border border-white/20 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <polyline stroke-linecap="round" stroke-linejoin="round" points="9 22 9 12 15 12 15 22"/>
                  </svg>
                </div>
                <span class="text-lg font-semibold tracking-tight">RealtyEmails</span>
              </div>

              {{-- Middle: tagline --}}
              <div>
                <p class="text-xs uppercase tracking-widest text-yellow-400 font-medium mb-3">Premium Real Estate Platform</p>
                <h2 class="text-4xl font-light leading-snug mb-4">
                  Where exceptional<br>properties find<br>their audience.
                </h2>
                <p class="text-sm text-white/50 max-w-xs leading-relaxed">
                  Manage listings, agent profiles, and email campaigns from one powerful dashboard.
                </p>
              </div>

              {{-- Bottom: agent avatars + property thumbs --}}
              <div class="space-y-4">

                {{-- Agents --}}
                <div class="flex items-center gap-3">
                  <div class="flex -space-x-2">
                    <img src="https://images.unsplash.com/photo-1573496799652-408c2ac9fe98?w=80&h=80&q=80&auto=format&fit=crop&crop=face" class="w-9 h-9 rounded-full border-2 border-white/30 object-cover" alt="">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=80&h=80&q=80&auto=format&fit=crop&crop=face" class="w-9 h-9 rounded-full border-2 border-white/30 object-cover" alt="">
                    <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=80&h=80&q=80&auto=format&fit=crop&crop=face" class="w-9 h-9 rounded-full border-2 border-white/30 object-cover" alt="">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&q=80&auto=format&fit=crop&crop=face" class="w-9 h-9 rounded-full border-2 border-white/30 object-cover" alt="">
                  </div>
                  <div class="text-xs text-white/60">
                    <span class="text-white font-medium">2,400+</span> agents across North America
                  </div>
                </div>

                {{-- Property thumbnails --}}
                <div class="grid grid-cols-3 gap-2">
                  <div class="relative rounded-lg overflow-hidden h-16">
                    <img src="https://images.unsplash.com/photo-1613977257363-707ba9348227?w=300&h=150&q=80&auto=format&fit=crop" class="w-full h-full object-cover brightness-75" alt="">
                    <span class="absolute bottom-1.5 left-2 text-white text-[10px] font-medium bg-[#1a2235]/60 px-1.5 py-0.5 rounded">$4.2M</span>
                  </div>
                  <div class="relative rounded-lg overflow-hidden h-16">
                    <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=300&h=150&q=80&auto=format&fit=crop" class="w-full h-full object-cover brightness-75" alt="">
                    <span class="absolute bottom-1.5 left-2 text-white text-[10px] font-medium bg-[#1a2235]/60 px-1.5 py-0.5 rounded">$2.8M</span>
                  </div>
                  <div class="relative rounded-lg overflow-hidden h-16">
                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=300&h=150&q=80&auto=format&fit=crop" class="w-full h-full object-cover brightness-75" alt="">
                    <span class="absolute bottom-1.5 left-2 text-white text-[10px] font-medium bg-[#1a2235]/60 px-1.5 py-0.5 rounded">$6.1M</span>
                  </div>
                </div>

              </div>
            </div>
          </div>

          {{-- ── RIGHT: Login Form ── --}}
          <div class="w-full lg:w-2/5 flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-sm">

              {{-- Mobile brand --}}
              <div class="flex items-center justify-center gap-2 mb-8 lg:hidden">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-xl font-semibold text-[#1a2235]">RealtyEmails</span>
              </div>

              {{-- Card --}}
              <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">

                {{-- Gold top accent --}}
                <div class="h-1 bg-gradient-to-r from-[#1a2235] via-yellow-400 to-[#1a2235]"></div>

                {{-- Card header --}}
                <div class="bg-[#1a2235] px-7 py-6">
                  <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 bg-white/10 rounded-md flex items-center justify-center">
                      <svg class="w-3.5 h-3.5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/>
                      </svg>
                    </div>
                    <span class="text-[10px] uppercase tracking-widest text-yellow-400 font-medium">Admin Portal</span>
                  </div>
                  <h1 class="text-white text-2xl font-semibold">Welcome back</h1>
                  <p class="text-white/40 text-sm mt-1">Sign in to your admin account</p>
                </div>

                {{-- Form --}}
                <div class="px-7 py-7">
                  <form method="POST" action="{{ route('admin.login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-5">
                      <label for="email" class="block text-[11px] font-semibold uppercase tracking-wider text-[#1a2235] mb-1.5">
                        Email Address
                      </label>
                      <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                          </svg>
                        </span>
                        <input
                          id="email" name="email" type="email"
                          autocomplete="email" required
                          value="{{ old('email') }}"
                          placeholder="admin@realty.com"
                          class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-800 placeholder-gray-400 bg-gray-50 focus:bg-white focus:border-[#1a2235] focus:ring-2 focus:ring-[#1a2235]/10 focus:outline-none transition @error('email') border-red-400 bg-red-50 @enderror"
                        />
                      </div>
                      @error('email')
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                          <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                          {{ $message }}
                        </p>
                      @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-5">
                      <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-[11px] font-semibold uppercase tracking-wider text-[#1a2235]">
                          Password
                        </label>
                        <a href="#" class="text-xs text-gray-400 hover:text-[#1a2235] transition">Forgot password?</a>
                      </div>
                      <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-gray-400">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/>
                          </svg>
                        </span>
                        <input
                          id="re-password" name="password" type="password"
                          autocomplete="current-password" required
                          placeholder="••••••••"
                          class="w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-800 placeholder-gray-400 bg-gray-50 focus:bg-white focus:border-[#1a2235] focus:ring-2 focus:ring-[#1a2235]/10 focus:outline-none transition @error('password') border-red-400 bg-red-50 @enderror"
                        />
                        <button type="button" onclick="reTogglePwd()"
                          class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-[#1a2235] transition">
                          <svg id="re-eye" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                          </svg>
                        </button>
                      </div>
                      @error('password')
                        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                          <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                          {{ $message }}
                        </p>
                      @enderror
                    </div>

                    {{-- Remember me --}}
                    <label class="flex items-center gap-2.5 mb-6 cursor-pointer">
                      <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-gray-300 accent-[#1a2235] cursor-pointer">
                      <span class="text-sm text-gray-500">Keep me signed in</span>
                    </label>

                    {{-- Session error --}}
                    @if(session('error'))
                      <div class="flex items-start gap-2.5 bg-red-50 border border-red-200 rounded-lg px-4 py-3 mb-5 text-sm text-red-700">
                        <svg class="w-4 h-4 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        {{ session('error') }}
                      </div>
                    @endif

                    {{-- Submit --}}
                    <button type="submit"
                      class="w-full bg-[#1a2235] hover:bg-[#243047] active:scale-[0.99] text-white text-sm font-medium tracking-wide py-3 rounded-lg transition shadow-sm hover:shadow-md">
                      Sign In
                    </button>

                  </form>
                </div>
              </div>

              <p class="text-center text-xs text-gray-400 mt-5">
                Restricted access &mdash; authorised personnel only
              </p>

            </div>
          </div>

        </div>

      </div>
    </div>
  </main>

  @include('public.layout.footer')

  <script>
    function reTogglePwd() {
      var inp  = document.getElementById('re-password');
      var icon = document.getElementById('re-eye');
      if (inp.type === 'password') {
        inp.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
      } else {
        inp.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
      }
    }
  </script>
</body>
</html>