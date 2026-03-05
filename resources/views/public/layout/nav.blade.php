{{-- NAVBAR --}}
<header class="relative w-full text-white">

  {{-- gradient background --}}
  <div class="absolute inset-0 bg-gradient-to-r from-[#1b2f63] via-[#223a75] to-[#2a4486]"></div>

  {{-- subtle bottom highlight --}}
  <div class="absolute bottom-0 left-0 w-full h-[1px] bg-white/10"></div>

  <div class="relative mx-auto max-w-screen-2xl px-6 lg:px-10">

    <div class="flex items-center justify-between h-[72px]">

      {{-- LOGO --}}
      <div class="flex items-center">

        <img
          src="{{ asset('images/realtyemails-logo.png') }}"
          alt="RealtyEmails"
          class="h-9 w-auto brightness-110"
        >

      </div>

      {{-- NAVIGATION --}}
      <nav class="hidden md:flex items-center gap-10 text-[15px] font-medium tracking-[.03em]">

        <a href="#" class="text-white/85 hover:text-white transition duration-200">
          Features
        </a>

        <a href="#" class="text-white/85 hover:text-white transition duration-200">
          Pricing
        </a>

        <a href="#" class="text-white/85 hover:text-white transition duration-200">
          Examples
        </a>

        <a href="#" class="text-white/85 hover:text-white transition duration-200">
          Support
        </a>

      </nav>

      {{-- RIGHT SIDE --}}
      <div class="flex items-center gap-6">

        {{-- SEARCH --}}
        <button class="text-white/80 hover:text-white transition">
          <i class="ti-search text-[18px]"></i>
        </button>

        {{-- LOGIN --}}
        <a href="#" class="text-white/85 hover:text-white text-[14px] font-medium transition">
          Log in
        </a>

        {{-- SIGN UP --}}
        <a
          href="#"
          class="rounded-full bg-white/15 border border-white/20 px-4 py-[6px] text-[14px] font-semibold tracking-[.02em] backdrop-blur-sm hover:bg-white/25 transition duration-200"
        >
          Sign Up
        </a>

      </div>

    </div>

  </div>

</header>