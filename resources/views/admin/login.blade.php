@include('public.layout.head')

<body class="relative bg-[#1a2235] min-h-screen font-sans text-gray-800 postgres">

  @include('public.layout.nav')

  <main class="transition-all duration-300 min-h-screen pt-24 relative"
  :class="collapsed ? 'ml-20' : 'ml-64'">

    {{-- Full-bleed wrapper with navy bg so there's no white void --}}
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10 pb-10">
      <div class="pageswap w-full">

        {{-- Outer card: image left + form right, same height, flush together --}}
        <div class="flex flex-col lg:flex-row rounded-2xl overflow-hidden shadow-2xl min-h-[680px]">

          {{-- ── LEFT: Hero Image ── --}}
          <div class="relative lg:w-3/5 min-h-[320px] lg:min-h-0">

            <img
              src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1400&q=85&auto=format&fit=crop"
              alt="Luxury Property"
              class="absolute inset-0 w-full h-full object-cover"
            />

            {{-- Gradient: dark bottom + subtle left fade for text legibility --}}
            <div class="absolute inset-0 bg-gradient-to-t from-[#1a2235] via-[#1a2235]/30 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#1a2235]/60 via-transparent to-transparent"></div>

            {{-- Content --}}
            <div class="relative z-10 flex flex-col justify-between h-full p-8 lg:p-10 text-white min-h-[320px] lg:min-h-0">

              {{-- Brand --}}
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-white/10 border border-white/20 rounded-lg flex items-center justify-center">
                  <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <polyline stroke-linecap="round" stroke-linejoin="round" points="9 22 9 12 15 12 15 22"/>
                  </svg>
                </div>
                <span class="text-base font-semibold tracking-tight">RealtyEmails</span>
              </div>

              {{-- Tagline --}}
              <div class="mt-auto">
                <p class="text-[10px] uppercase tracking-widest text-yellow-400 font-semibold mb-3">Premium Real Estate Platform</p>
                <h2 class="text-3xl lg:text-4xl font-light leading-snug mb-3">
                  Where exceptional<br>properties find<br>their audience.
                </h2>
                <p class="text-sm text-white/50 max-w-xs leading-relaxed mb-6">
                  Manage listings, agents, and email campaigns from one powerful dashboard.
                </p>

                {{-- Agents --}}
                <div class="flex items-center gap-3 mb-4">
                  <div class="flex -space-x-2">
                    <img src="https://images.unsplash.com/photo-1573496799652-408c2ac9fe98?w=80&h=80&q=80&auto=format&fit=crop&crop=face" class="w-8 h-8 rounded-full border-2 border-white/30 object-cover" alt="">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=80&h=80&q=80&auto=format&fit=crop&crop=face" class="w-8 h-8 rounded-full border-2 border-white/30 object-cover" alt="">
                    <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=80&h=80&q=80&auto=format&fit=crop&crop=face" class="w-8 h-8 rounded-full border-2 border-white/30 object-cover" alt="">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&q=80&auto=format&fit=crop&crop=face" class="w-8 h-8 rounded-full border-2 border-white/30 object-cover" alt="">
                  </div>
                  <span class="text-xs text-white/60"><span class="text-white font-medium">2,400+</span> agents across North America</span>
                </div>

                {{-- Property thumbnails --}}
                <div class="grid grid-cols-3 gap-2 max-w-xs">
                  <div class="relative rounded-lg overflow-hidden h-14">
                    <img src="https://images.unsplash.com/photo-1613977257363-707ba9348227?w=300&h=150&q=80&auto=format&fit=crop" class="w-full h-full object-cover brightness-75" alt="">
                    <span class="absolute bottom-1 left-1.5 text-white text-[9px] font-semibold bg-black/50 px-1.5 py-0.5 rounded">$4.2M</span>
                  </div>
                  <div class="relative rounded-lg overflow-hidden h-14">
                    <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=300&h=150&q=80&auto=format&fit=crop" class="w-full h-full object-cover brightness-75" alt="">
                    <span class="absolute bottom-1 left-1.5 text-white text-[9px] font-semibold bg-black/50 px-1.5 py-0.5 rounded">$2.8M</span>
                  </div>
                  <div class="relative rounded-lg overflow-hidden h-14">
                    <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=300&h=150&q=80&auto=format&fit=crop" class="w-full h-full object-cover brightness-75" alt="">
                    <span class="absolute bottom-1 left-1.5 text-white text-[9px] font-semibold bg-black/50 px-1.5 py-0.5 rounded">$6.1M</span>
                  </div>
                </div>
              </div>

            </div>
          </div>

          {{-- ── RIGHT: Login Form — same navy bg as body, feels part of one unit ── --}}
          <div class="lg:w-2/5 bg-[#1a2235] flex items-center justify-center px-8 py-10 lg:py-0">
            <div class="w-full max-w-sm">

              {{-- Section label --}}
              <div class="flex items-center gap-2 mb-6">
                <div class="w-6 h-6 bg-white/10 rounded-md flex items-center justify-center">
                  <svg class="w-3 h-3 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/>
                  </svg>
                </div>
                <span class="text-[10px] uppercase tracking-widest text-yellow-400 font-semibold">Admin Portal</span>
              </div>

              <h1 class="text-white text-3xl font-semibold mb-1">Welcome back</h1>
              <p class="text-white/40 text-sm mb-8">Sign in to your admin account</p>

              <form method="POST" action="{{ route('admin.login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-4">
                  <label for="email" class="block text-[10px] font-semibold uppercase tracking-wider text-white/50 mb-1.5">
                    Email Address
                  </label>
                  <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-white/30">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                      </svg>
                    </span>
                    <input
                      id="email" name="email" type="email"
                      autocomplete="email" required
                      value="{{ old('email') }}"
                      placeholder="admin@realty.com"
                      class="w-full pl-10 pr-4 py-2.5 bg-white/5 border border-white/10 rounded-lg text-sm text-white placeholder-white/25 focus:bg-white/10 focus:border-yellow-400/60 focus:ring-1 focus:ring-yellow-400/30 focus:outline-none transition @error('email') border-red-400/60 @enderror"
                    />
                  </div>
                  @error('email')
                    <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                      <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                      {{ $message }}
                    </p>
                  @enderror
                </div>

                {{-- Password --}}
                <div class="mb-5">
                  <div class="flex justify-between items-center mb-1.5">
                    <label for="re-password" class="block text-[10px] font-semibold uppercase tracking-wider text-white/50">
                      Password
                    </label>
                    <a href="#" class="text-xs text-white/30 hover:text-yellow-400 transition">Forgot password?</a>
                  </div>
                  <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-white/30">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/>
                      </svg>
                    </span>
                    <input
                      id="re-password" name="password" type="password"
                      autocomplete="current-password" required
                      placeholder="••••••••"
                      class="w-full pl-10 pr-10 py-2.5 bg-white/5 border border-white/10 rounded-lg text-sm text-white placeholder-white/25 focus:bg-white/10 focus:border-yellow-400/60 focus:ring-1 focus:ring-yellow-400/30 focus:outline-none transition @error('password') border-red-400/60 @enderror"
                    />
                    <button type="button" onclick="reTogglePwd()"
                      class="absolute inset-y-0 right-3 flex items-center text-white/30 hover:text-yellow-400 transition">
                      <svg id="re-eye" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                      </svg>
                    </button>
                  </div>
                  @error('password')
                    <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                      <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                      {{ $message }}
                    </p>
                  @enderror
                </div>

                {{-- Remember me --}}
                <label class="flex items-center gap-2.5 mb-6 cursor-pointer">
                  <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-white/20 bg-white/5 accent-yellow-400 cursor-pointer">
                  <span class="text-sm text-white/40">Keep me signed in</span>
                </label>

                {{-- Session error --}}
                @if(session('error'))
                  <div class="flex items-start gap-2.5 bg-red-500/10 border border-red-500/20 rounded-lg px-4 py-3 mb-5 text-sm text-red-400">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    {{ session('error') }}
                  </div>
                @endif

                {{-- Submit --}}
                <button type="submit"
                  class="w-full bg-yellow-400 hover:bg-yellow-300 active:scale-[0.99] text-[#1a2235] text-sm font-bold tracking-wide py-3 rounded-lg transition shadow-lg shadow-yellow-400/10 hover:shadow-yellow-400/20">
                  Sign In
                </button>

              </form>

              <p class="text-center text-xs text-white/20 mt-6">
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