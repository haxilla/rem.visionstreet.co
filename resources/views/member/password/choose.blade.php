@extends('member.password.layout')
@section('title', 'Choose your account')

@section('content')
    <h1 class="font-display text-[32px] font-bold leading-none tracking-tight text-brand-bluedark">Which account?</h1>
    <p class="mt-3 text-[14px] leading-relaxed text-slate-500">
        More than one RealtyEmails account uses this email. Choose the one you want to open now
        &mdash; you can sign out and open another any time.
    </p>

    <form method="POST" action="{{ route('member.login.account.choose') }}" class="mt-6 space-y-3">
        @csrf

        @foreach($accounts as $a)
            <button type="submit" name="agent" value="{{ $a['id'] }}"
                    class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-left transition hover:border-brand-blue hover:bg-white hover:ring-4 hover:ring-brand-blue/10 focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                <span class="block text-[15px] font-semibold text-brand-bluedark">{{ $a['name'] }}</span>
                @if($a['office'])
                    <span class="mt-0.5 block text-[13px] text-slate-500">{{ $a['office'] }}</span>
                @endif
                <span class="mt-1 block text-[12px] text-slate-400">
                    {{ $a['flyers'] }} {{ \Illuminate\Support\Str::plural('flyer', $a['flyers']) }}
                    @if($a['started'])
                        &middot; member since {{ \Carbon\Carbon::parse($a['started'])->format('M Y') }}
                    @endif
                    &middot; account #{{ $a['id'] }}
                </span>
            </button>
        @endforeach
    </form>

    <p class="mt-6 text-center text-[14px]">
        <a href="{{ route('member.login') }}" class="font-semibold text-brand-blue hover:underline">&larr; Back to sign in</a>
    </p>
@endsection
