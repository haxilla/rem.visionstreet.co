@php
    $navAdmin = auth('admin')->user();

    $navAdminName = trim(($navAdmin->adminFirst ?? '') . ' ' . ($navAdmin->adminLast ?? ''));

    $navInitials = '?';
    if ($navAdmin && $navAdminName !== '') {
        $navNameParts = preg_split('/\s+/', $navAdminName);
        $navInitials = strtoupper(substr($navNameParts[0], 0, 1) . substr(end($navNameParts), 0, 1));
    } elseif ($navAdmin && $navAdmin->adminEmail) {
        $navInitials = strtoupper(substr($navAdmin->adminEmail, 0, 1));
    }
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
      <a href="/admin/dashboard" class="flex items-center shrink-0">
        <img src="{{ asset('images/RealtyEmails_logo1.png') }}" alt="RealtyEmails" class="h-9 w-auto">
      </a>

      {{-- DESKTOP NAV LINKS (absolutely centered on the header, so the
           logo/avatar being different widths can't push it off-center -
           justify-between alone only guarantees equal gaps, not a
           centered middle element) --}}
      <nav class="hidden lg:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 items-center gap-8 text-[14px] font-medium tracking-[0.02em] text-white/80">
        <a href="/admin/dashboard" class="hover:text-white transition">Dashboard</a>
        <a href="/admin/agents" class="hover:text-white transition">Agents</a>
      </nav>

      {{-- RIGHT SIDE --}}
      <div class="flex items-center gap-3">

        {{-- ACCOUNT DROPDOWN (desktop) --}}
        <details class="relative hidden lg:block">
          <summary class="flex cursor-pointer list-none items-center rounded-full transition">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 text-sm font-black text-white ring-2 ring-white/25 hover:ring-white/60 transition">
              {{ $navInitials }}
            </span>
          </summary>

          <div class="absolute right-0 top-[calc(100%+10px)] w-64 rounded-2xl bg-white p-2 text-slate-700 shadow-xl ring-1 ring-black/10">
            <div class="flex items-center gap-3 px-3 py-3">
              <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[#123f91]/10 text-sm font-black text-[#123f91] ring-1 ring-slate-200">
                {{ $navInitials }}
              </span>
              <div class="min-w-0">
                <div class="truncate text-sm font-black text-slate-900">{{ $navAdminName !== '' ? $navAdminName : 'Admin' }}</div>
                @if($navAdmin && $navAdmin->adminEmail)
                  <div class="truncate text-xs text-slate-500">{{ $navAdmin->adminEmail }}</div>
                @endif
              </div>
            </div>
            <div class="mb-1 h-px bg-slate-100"></div>
            <a href="{{ route('admin.logout') }}" class="block rounded-xl px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50">Log out</a>
          </div>
        </details>

        {{-- MOBILE HAMBURGER --}}
        <details class="relative lg:hidden">
          <summary class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-lg text-white hover:bg-white/10 transition">
            <span class="text-xl leading-none">&#9776;</span>
          </summary>

          <div class="absolute right-0 top-[calc(100%+8px)] w-64 max-w-[calc(100vw-24px)] rounded-2xl bg-white p-2 text-slate-700 shadow-xl ring-1 ring-black/10">
            <div class="flex items-center gap-3 px-3 py-3">
              <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[#123f91]/10 text-sm font-black text-[#123f91] ring-1 ring-slate-200">
                {{ $navInitials }}
              </span>
              <div class="min-w-0">
                <div class="truncate text-sm font-black text-slate-900">{{ $navAdminName !== '' ? $navAdminName : 'Admin' }}</div>
                @if($navAdmin && $navAdmin->adminEmail)
                  <div class="truncate text-xs text-slate-500">{{ $navAdmin->adminEmail }}</div>
                @endif
              </div>
            </div>
            <div class="mb-1 h-px bg-slate-100"></div>
            <a href="/admin/dashboard" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Dashboard</a>
            <a href="/admin/agents" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100">Agents</a>
            <div class="my-1 h-px bg-slate-100"></div>
            <a href="{{ route('admin.logout') }}" class="block rounded-xl px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50">Log out</a>
          </div>
        </details>

      </div>

    </div>
  </div>
</header>
