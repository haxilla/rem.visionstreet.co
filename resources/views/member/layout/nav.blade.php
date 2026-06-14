{{-- FIXED NAVBAR --}}
<header class="fixed top-0 left-0 w-full z-50">

  {{-- background --}}
  <div class="absolute inset-0 bg-gradient-to-r from-[#1b2f63] via-[#223a75] to-[#2a4486]"></div>

  {{-- bottom divider --}}
  <div class="absolute bottom-0 left-0 right-0 h-px bg-white/10"></div>

  <div class="relative mx-auto max-w-screen-2xl px-6 lg:px-10" style="max-width:1600px;">
    <div class="flex h-[72px] items-center justify-between text-white">

      {{-- LOGO --}}
      <a href="/" class="flex items-center">
        <img src="{{ asset('images/RealtyEmails_logo1.png') }}" alt="RealtyEmails" class="h-9 w-auto">
      </a>

      {{-- NAV LINKS --}}
      <nav class="hidden md:flex items-center gap-10 text-[14px] font-medium tracking-[0.06em] text-white/80">

        YOU ARE LOGGED IN

      </nav>

      {{-- RIGHT SIDE --}}
      <div class="flex items-center gap-5">

        <a href="/logout" 
        class="text-[14px] font-medium text-white/80 hover:text-white transition">
          Log out
        </a>

      </div>

    </div>
  </div>
</header>