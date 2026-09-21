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

    // The menu comes from ONE list (App\Support\AdminMenu) - add a page there, not here.
    $navMenu = \App\Support\AdminMenu::items();
@endphp

{{-- Menu look: plain CSS so it renders the same whether or not the site stylesheet has been
     rebuilt since the last deploy (this bar is on every admin page). --}}
<style>
    .an-link      { position: relative; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; list-style: none;
                    color: rgba(255,255,255,.8); text-decoration: none; transition: color .12s; }
    .an-link:hover { color: #fff; }
    .an-link::-webkit-details-marker { display: none; }
    .an-link.is-on { color: #fff; }
    .an-link.is-on::after { content: ""; position: absolute; left: 0; right: 0; bottom: -8px; height: 2px; border-radius: 2px; background: #fff; }
    .an-caret     { width: 10px; height: 10px; opacity: .7; transition: transform .15s; }
    details.an-group[open] .an-caret { transform: rotate(180deg); }

    .an-group     { position: relative; }
    .an-panel     { position: absolute; top: calc(100% + 18px); left: 50%; transform: translateX(-50%); min-width: 190px; padding: 8px;
                    border-radius: 16px; background: #fff; color: #334155; box-shadow: 0 18px 40px rgba(15,23,42,.22);
                    border: 1px solid rgba(15,23,42,.08); }
    .an-item      { display: block; padding: 10px 14px; border-radius: 10px; text-decoration: none; color: #334155; }
    .an-item:hover { background: #f1f5f9; }
    .an-item.is-on { background: #eef3ff; }
    .an-item-t    { display: block; font-size: 14px; font-weight: 700; color: #0f172a; }
    .an-item-n    { display: block; margin-top: 2px; font-size: 12px; font-weight: 500; color: #64748b; line-height: 1.35; }

    /* mobile menu: a group is a small heading with its links under it */
    .an-mh        { padding: 10px 16px 4px; font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: #94a3b8; }
    .an-m-on      { background: #eef3ff; color: #123f91; }
</style>

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
           centered middle element). Links and drop-down groups both come
           from App\Support\AdminMenu. --}}
      <nav class="hidden lg:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 items-center gap-8 text-[14px] font-medium tracking-[0.02em]">
        @foreach($navMenu as $navEntry)
          @if(!empty($navEntry['items']))
            <details class="an-group">
              <summary class="an-link {{ \App\Support\AdminMenu::isActive($navEntry) ? 'is-on' : '' }}">
                {{ $navEntry['label'] }}
                <svg class="an-caret" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 4.5 6 8.5 10 4.5"/></svg>
              </summary>

              <div class="an-panel">
                @foreach($navEntry['items'] as $navChild)
                  <a href="{{ $navChild['href'] }}" class="an-item {{ \App\Support\AdminMenu::isActive($navChild) ? 'is-on' : '' }}">
                    <span class="an-item-t">{{ $navChild['label'] }}</span>
                    @if(!empty($navChild['note']))
                      <span class="an-item-n">{{ $navChild['note'] }}</span>
                    @endif
                  </a>
                @endforeach
              </div>
            </details>
          @else
            <a href="{{ $navEntry['href'] }}" class="an-link {{ \App\Support\AdminMenu::isActive($navEntry) ? 'is-on' : '' }}">{{ $navEntry['label'] }}</a>
          @endif
        @endforeach
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

            @foreach($navMenu as $navEntry)
              @if(!empty($navEntry['items']))
                <div class="an-mh">{{ $navEntry['label'] }}</div>
                @foreach($navEntry['items'] as $navChild)
                  <a href="{{ $navChild['href'] }}" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100 {{ \App\Support\AdminMenu::isActive($navChild) ? 'an-m-on' : '' }}">{{ $navChild['label'] }}</a>
                @endforeach
              @else
                <a href="{{ $navEntry['href'] }}" class="block rounded-xl px-4 py-2.5 text-sm font-bold hover:bg-slate-100 {{ \App\Support\AdminMenu::isActive($navEntry) ? 'an-m-on' : '' }}">{{ $navEntry['label'] }}</a>
              @endif
            @endforeach

            <div class="my-1 h-px bg-slate-100"></div>
            <a href="{{ route('admin.logout') }}" class="block rounded-xl px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50">Log out</a>
          </div>
        </details>

      </div>

    </div>
  </div>
</header>

{{-- A menu group closes when you click elsewhere, and only one is open at a time. --}}
<script>
(function () {
    var groups = document.querySelectorAll('details.an-group');

    document.addEventListener('click', function (e) {
        groups.forEach(function (d) {
            if (d.open && !d.contains(e.target)) { d.removeAttribute('open'); }
        });
    });

    groups.forEach(function (d) {
        d.addEventListener('toggle', function () {
            if (!d.open) { return; }
            groups.forEach(function (other) { if (other !== d) { other.removeAttribute('open'); } });
        });
    });
})();
</script>
