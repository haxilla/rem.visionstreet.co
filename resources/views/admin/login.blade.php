@include('public.layout.head')

<body data-section="admin" 
class="linkcheck relative bg-white min-h-screen font-sans text-gray-800 postgres">

  @include('public.layout.nav')

  <main class="transition-all duration-300 min-h-screen pt-24 relative"
  :class="collapsed ? 'ml-20' : 'ml-64'">
    <div class="ml-8 mr-8 lg:ml-10 lg:mr-10">
      <div class="pageswap p-6 w-full">

        <div class="min-h-[calc(100vh-12rem)] flex items-center justify-center">
          <div class="w-full max-w-md">

            <!-- Card -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden">

              <!-- Header bar -->
              <div class="bg-gray-900 px-8 py-6">
                <div class="flex items-center gap-3 mb-1">
                  <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                  </div>
                  <span class="text-white/50 text-xs font-medium tracking-widest uppercase">Admin Portal</span>
                </div>
                <h1 class="text-white text-2xl font-semibold tracking-tight">Welcome back</h1>
                <p class="text-white/50 text-sm mt-1">Sign in to your admin account</p>
              </div>

              <!-- Form -->
              <div class="px-8 py-8">
                <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
                  @csrf

                  <!-- Email -->
                  <div>
                    <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                      Email Address
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                      </div>
                      <input
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        required
                        value="{{ old('email') }}"
                        class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm text-gray-800
                               placeholder-gray-400 bg-gray-50 focus:bg-white focus:border-gray-900
                               focus:ring-2 focus:ring-gray-900/10 focus:outline-none transition-all duration-200
                               @error('email') border-red-400 bg-red-50 @enderror"
                        placeholder="admin@example.com"
                      />
                    </div>
                    @error('email')
                      <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                      </p>
                    @enderror
                  </div>

                  <!-- Password -->
                  <div>
                    <div class="flex items-center justify-between mb-2">
                      <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Password
                      </label>
                      <a href="#"
                         class="text-xs text-gray-400 hover:text-gray-900 transition-colors duration-150">
                        Forgot password?
                      </a>
                    </div>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                      </div>
                      <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl text-sm text-gray-800
                               placeholder-gray-400 bg-gray-50 focus:bg-white focus:border-gray-900
                               focus:ring-2 focus:ring-gray-900/10 focus:outline-none transition-all duration-200
                               @error('password') border-red-400 bg-red-50 @enderror"
                        placeholder="••••••••"
                      />
                      <!-- Toggle visibility -->
                      <button type="button" onclick="togglePassword()"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-700 transition-colors">
                        <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                      </button>
                    </div>
                    @error('password')
                      <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                      </p>
                    @enderror
                  </div>

                  <!-- Remember me -->
                  <div class="flex items-center gap-3">
                    <input
                      id="remember"
                      name="remember"
                      type="checkbox"
                      class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900/20 cursor-pointer"
                    />
                    <label for="remember" class="text-sm text-gray-500 cursor-pointer select-none">
                      Keep me signed in
                    </label>
                  </div>

                  <!-- Session / general error -->
                  @if(session('error'))
                    <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                      <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                      </svg>
                      <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                  @endif

                  <!-- Submit -->
                  <button
                    type="submit"
                    class="w-full bg-gray-900 hover:bg-gray-800 active:bg-black text-white font-semibold
                           py-3 px-6 rounded-xl text-sm tracking-wide transition-all duration-150
                           shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-gray-900/30 mt-2">
                    Sign In
                  </button>

                </form>
              </div>

            </div>

            <!-- Footer note -->
            <p class="text-center text-xs text-gray-400 mt-6">
              Restricted access &mdash; authorised personnel only
            </p>

          </div>
        </div>

      </div>
    </div>
  </main>

  @include('public.layout.footer')

  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      const icon  = document.getElementById('eye-icon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7
               a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878
               l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59
               m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025
               0 01-4.132 5.411m0 0L21 21"/>`;
      } else {
        input.type = 'password';
        icon.innerHTML = `
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943
               9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
      }
    }
  </script>
</body>
</html>