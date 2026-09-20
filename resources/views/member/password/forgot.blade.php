@extends('member.password.layout')
@section('title', 'Create your password')

@section('content')
    <h1 class="font-display text-[32px] font-bold leading-none tracking-tight text-brand-bluedark">Create your password</h1>
    <p class="mt-3 text-[14px] leading-relaxed text-slate-500">
        Enter the email you use with RealtyEmails and we'll email you a link to set a password.
        This is for agents on our new site for the first time, and for anyone who forgot their password.
        Passwords from the old site can't be used.
    </p>

    <form method="POST" action="{{ route('member.password.forgot.send') }}" class="mt-6 space-y-5">
        @csrf
        <div>
            <label for="xxAgtUname" class="mb-2 block text-[11px] font-semibold uppercase tracking-[.08em] text-brand-blue">Email Address</label>
            <input type="email" id="xxAgtUname" name="xxAgtUname" value="{{ old('xxAgtUname') }}" required autofocus autocomplete="email"
                   placeholder="you@example.com"
                   class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-[15px] text-slate-800 outline-none transition focus:border-brand-blue focus:bg-white focus:ring-4 focus:ring-brand-blue/10">
        </div>

        <button type="submit"
                class="w-full rounded-2xl bg-brand-blue py-4 text-[15px] font-semibold text-white shadow-lg shadow-brand-blue/25 transition hover:bg-brand-bluemid">
            Email me a link
        </button>
    </form>

    <p class="mt-6 text-center text-[14px]">
        <a href="{{ route('member.login') }}" class="font-semibold text-brand-blue hover:underline">&larr; Back to sign in</a>
    </p>
@endsection
