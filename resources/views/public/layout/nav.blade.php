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

        <a href="/features" class="relative group hover:text-white transition">
          Features
          <span class="absolute left-1/2 -bottom-1 h-[2px] w-0 -translate-x-1/2 bg-white/60 rounded-full transition-all group-hover:w-8"></span>
        </a>

        <a href="/pricing" class="relative group hover:text-white transition">
          Pricing
          <span class="absolute left-1/2 -bottom-1 h-[2px] w-0 -translate-x-1/2 bg-white/60 rounded-full transition-all group-hover:w-8"></span>
        </a>

        <a href="/examples" class="relative group hover:text-white transition">
          Examples
          <span class="absolute left-1/2 -bottom-1 h-[2px] w-0 -translate-x-1/2 bg-white/60 rounded-full transition-all group-hover:w-8"></span>
        </a>

        <a href="/support" class="relative group hover:text-white transition">
          Support
          <span class="absolute left-1/2 -bottom-1 h-[2px] w-0 -translate-x-1/2 bg-white/60 rounded-full transition-all group-hover:w-8"></span>
        </a>

      </nav>

      {{-- RIGHT SIDE --}}
      <div class="flex items-center gap-5">

        <button class="text-white/80 hover:text-white transition">
          <i class="ti-search text-[16px]"></i>
        </button>

        <a href="#"
        data-action="handle"
        data-modalid="member-login-modal"
        data-renderto="member-login-modal-content"
        data-renderfrom="member.login.modal"
        data-renderas="html" 
        class="text-[14px] font-medium text-white/80 hover:text-white transition">
          Log in
        </a>

        <a
          href="#"
          class="rounded-full border border-white/20 bg-white/10 px-4 py-2 text-[13px] font-semibold text-white backdrop-blur-sm hover:bg-white/15 transition"
        >
          Sign Up
        </a>

      </div>

    </div>
  </div>
</header>