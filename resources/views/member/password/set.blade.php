@extends('member.password.layout')
@section('title', $invalid ? 'Link expired' : 'Create your new password')

@section('content')
@if($invalid)

    <h1 class="font-display text-[32px] font-bold leading-none tracking-tight text-brand-bluedark">This link has expired</h1>
    <p class="mt-3 text-[14px] leading-relaxed text-slate-500">
        Password links work once and only for {{ \App\Support\AgentPasswords::LINK_MINUTES }} minutes, or this one has
        been replaced by a newer email. Request a fresh one below.
    </p>
    <a href="{{ route('member.password.forgot') }}"
       class="mt-6 block w-full rounded-2xl bg-brand-blue py-4 text-center text-[15px] font-semibold text-white shadow-lg shadow-brand-blue/25 transition hover:bg-brand-bluemid">
        Email me a new link
    </a>

@else

    <h1 class="font-display text-[32px] font-bold leading-none tracking-tight text-brand-bluedark">Create your new password</h1>
    <p class="mt-3 text-[14px] leading-relaxed text-slate-500">
        You can't reuse your old password. Choose a new one that meets all of these:
    </p>

    <ul id="rules" class="mt-4 space-y-1.5 text-[13px] text-slate-500">
        @foreach($requirements as $r)
            <li class="flex items-center gap-2"><span class="tick inline-block h-4 w-4 rounded-full border border-slate-300 text-center text-[10px] leading-[14px] text-white"></span>{{ $r }}</li>
        @endforeach
    </ul>

    <form method="POST" action="{{ route('member.password.set.save', $token) }}" class="mt-6 space-y-5" autocomplete="off">
        @csrf
        <div>
            <label for="password" class="mb-2 block text-[11px] font-semibold uppercase tracking-[.08em] text-brand-blue">New password</label>
            <input type="password" id="password" name="password" required autofocus autocomplete="new-password"
                   class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-[15px] text-slate-800 outline-none transition focus:border-brand-blue focus:bg-white focus:ring-4 focus:ring-brand-blue/10">
        </div>
        <div>
            <label for="password_confirmation" class="mb-2 block text-[11px] font-semibold uppercase tracking-[.08em] text-brand-blue">Type it again</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                   class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-[15px] text-slate-800 outline-none transition focus:border-brand-blue focus:bg-white focus:ring-4 focus:ring-brand-blue/10">
        </div>
        <label class="flex cursor-pointer select-none items-center gap-2 text-[13px] text-slate-500">
            <input type="checkbox" id="show" class="h-4 w-4 accent-brand-blue"> Show passwords
        </label>

        <button type="submit"
                class="w-full rounded-2xl bg-brand-blue py-4 text-[15px] font-semibold text-white shadow-lg shadow-brand-blue/25 transition hover:bg-brand-bluemid">
            Set my password
        </button>
    </form>

    <script>
        (function () {
            var pw = document.getElementById('password');
            var ticks = document.querySelectorAll('#rules .tick');
            var checks = [
                function (v) { return v.length >= 12; },
                function (v) { return /[a-z]/.test(v) && /[A-Z]/.test(v); },
                function (v) { return /[0-9]/.test(v); },
                function (v) { return /[^A-Za-z0-9]/.test(v); }
            ];
            pw.addEventListener('input', function () {
                checks.forEach(function (ok, i) {
                    var pass = ok(pw.value);
                    ticks[i].textContent = pass ? '✓' : '';
                    ticks[i].style.background = pass ? '#059669' : '';
                    ticks[i].style.borderColor = pass ? '#059669' : '';
                });
            });
            document.getElementById('show').addEventListener('change', function (e) {
                var t = e.target.checked ? 'text' : 'password';
                pw.type = t;
                document.getElementById('password_confirmation').type = t;
            });
        })();
    </script>

@endif
@endsection
