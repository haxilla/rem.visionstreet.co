@php
    $navAgent = auth('member')->user();
@endphp

{{-- FIXED NAVBAR --}}
<header class="fixed top-0 left-0 w-full z-50">

  {{-- background --}}
  <div class="absolute inset-0 bg-gradient-to-r from-[#1b2f63] via-[#223a75] to-[#2a4486]"></div>

  {{-- bottom divider --}}
  <div class="absolute bottom-0 left-0 right-0 h-px bg-white/10"></div>

  <div class="relative mx-auto max-w-screen-2xl px-6 lg:px-10" style="max-width:1600px;">
    <div class="flex h-[72px] items-center justify-between text-white">

      {{-- LOGO --}}
      <a href="/member/dashboard" class="flex items-center shrink-0">
        <img src="{{ asset('images/RealtyEmails_logo1.png') }}" alt="RealtyEmails" class="h-9 w-auto">
      </a>

      {{-- DESKTOP NAV LINKS --}}
      <nav class="hidden md:flex items-center gap-8 text-[14px] font-medium tracking-[0.02em] text-white/80">
        <a href="/member/flyer/create" class="hover:text-white transition">Create New Flyer</a>
        <a href="/member/dashboard" class="hover:text-white transition">My Flyers</a>
        <a href="/member/campaigns" class="hover:text-white transition">Campaigns</a>
        <a href="/member/copy-center" class="hover:text-white transition">Copy Center</a>
        <a href="/member/buy-credits" class="hover:text-white transition">Buy Credits</a>
      </nav>

      {{-- RIGHT SIDE --}}
      <div class="flex items-center gap-3">

        {{-- ACCOUNT DROPDOWN (desktop) --}}
        <details class="relative hidden md:block">
          <summary class="flex cursor-pointer list-none items-center gap-2 rounded-lg px-3 py-2 text-[14px] font-medium text-white/80 hover:bg-white/10 hover:text-white transition">
            {{ $navAgent->agtFullName ?? 'Account' }}
            <svg class="h-3 w-3" viewBox="0 0 12 12" fill="none">
              <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </summary>

          <div class="absolute right-0 top-[calc(100%+8px)] w-56 rounded-2xl bg-white p-2 text-slate-700 shadow-xl ring-1 ring-black/10">
            <a href="/member/agent-info" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Agent Info</a>
            <a href="/member/account" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Account Info</a>
            <div class="my-1 h-px bg-slate-100"></div>
            <a href="/logout" class="block rounded-xl px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50">Log out</a>
          </div>
        </details>

        {{-- MOBILE HAMBURGER --}}
        <details class="relative md:hidden">
          <summary class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-lg text-white hover:bg-white/10 transition">
            <span class="text-xl leading-none">&#9776;</span>
          </summary>

          <div class="absolute right-0 top-[calc(100%+8px)] w-64 max-w-[calc(100vw-24px)] rounded-2xl bg-white p-2 text-slate-700 shadow-xl ring-1 ring-black/10">
            <a href="/member/flyer/create" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Create New Flyer</a>
            <a href="/member/dashboard" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">My Flyers</a>
            <a href="/member/campaigns" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Campaigns</a>
            <a href="/member/copy-center" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Copy Center</a>
            <a href="/member/buy-credits" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Buy Credits</a>
            <div class="my-1 h-px bg-slate-100"></div>
            <a href="/member/agent-info" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Agent Info</a>
            <a href="/member/account" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Account Info</a>
            <div class="my-1 h-px bg-slate-100"></div>
            <a href="/logout" class="block rounded-xl px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50">Log out</a>
          </div>
        </details>

      </div>

    </div>
  </div>
</header>
