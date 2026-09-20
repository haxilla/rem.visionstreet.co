{{-- Shared page shell for the forgot-password and set-new-password pages. Same look as the
     member login (Tailwind via the CDN, same brand colours). --}}
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <meta name="referrer" content="no-referrer">
    <title>@yield('title') — RealtyEmails</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            colors: { brand: { blue: '#214e9b', bluedark: '#0d1b3e', bluemid: '#1a3a70', gold: '#e79a63', golddark: '#c77d40' } },
            fontFamily: { display: ['"Cormorant Garamond"', 'Georgia', 'serif'], body: ['"DM Sans"', 'sans-serif'] },
        } } }
    </script>
</head>
<body class="min-h-full bg-slate-100 font-body">
    <div class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            <a href="{{ route('member.login') }}" class="mb-6 flex items-center justify-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-gold">
                    <svg class="h-5 w-5 fill-white" viewBox="0 0 24 24"><path d="M12 2L2 9.5V22h7v-7h6v7h7V9.5L12 2z"/></svg>
                </span>
                <span class="font-display text-[26px] font-bold tracking-tight text-brand-bluedark">Realty<span class="text-brand-gold">Emails</span></span>
            </a>

            <div class="relative overflow-hidden rounded-3xl bg-white px-6 py-9 shadow-2xl shadow-brand-bluedark/20 sm:px-10">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand-blue to-brand-gold"></div>

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[13px] text-red-700">
                        @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                @endif

                @yield('content')
            </div>

            <p class="mt-6 text-center text-[12px] text-slate-400">&copy; {{ date('Y') }} RealtyEmails. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
