{{-- NAVIGATION --}}
<header class="w-full bg-[#0f1e46] text-white">
  <div class="mx-auto max-w-screen-2xl px-6 lg:px-10">
    
    <div class="flex items-center justify-between h-20">

      {{-- LEFT: Logo --}}
      <div class="flex items-center gap-4">

        <img 
          src="/images/realtyemails-logo.png"
          alt="RealtyEmails"
          class="h-10 w-auto"
        >

      </div>

      {{-- CENTER: Navigation --}}
      <nav class="hidden md:flex items-center gap-8 text-[15px] font-medium text-white/90">

        <a href="#" class="hover:text-white transition">
          Features
        </a>

        <a href="#" class="hover:text-white transition">
          Pricing
        </a>

        <a href="#" class="hover:text-white transition">
          Examples
        </a>

        <a href="#" class="hover:text-white transition">
          Support
        </a>

      </nav>

      {{-- RIGHT: Auth --}}
      <div class="flex items-center gap-5">

        {{-- Search icon --}}
        <button class="text-white/80 hover:text-white transition">
          <i class="ti-search text-[18px]"></i>
        </button>

        <a href="#" class="text-white/80 hover:text-white transition text-[14px]">
          Log in
        </a>

        <a 
          href="#" 
          class="rounded-full bg-white/15 px-4 py-1.5 text-[14px] font-medium hover:bg-white/25 transition border border-white/20"
        >
          Sign Up
        </a>

      </div>

    </div>

  </div>
</header>