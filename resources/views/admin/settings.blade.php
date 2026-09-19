@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')

@php
    $definitions = $data['definitions'] ?? [];
    $values      = $data['values'] ?? [];
    $trialOn     = ($values['trial_mode'] ?? '0') === '1';
@endphp

<main class="min-h-screen bg-[#f4f7fb] pt-24">
<div class="px-4 py-6 sm:px-6 lg:px-10 lg:py-8">

    {{-- HEADER --}}
    <div class="rounded-[24px] bg-white px-5 py-6 shadow-[0_12px_35px_rgba(15,23,42,0.06)] sm:px-8 sm:py-7">
        <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
            Admin / Settings
        </div>

        <h1 class="mt-2 text-2xl font-semibold text-slate-900 sm:text-[32px]">
            System Settings
        </h1>

        <p class="mt-2 text-[14px] text-slate-600">
            These settings apply to the whole system, not to a single flyer or agent.
        </p>
    </div>

    @if(session('status'))
        <div class="mt-6 rounded-2xl border border-emerald-300 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if($trialOn)
        <div class="mt-6 rounded-2xl border border-amber-300 bg-amber-50 px-5 py-4 text-sm font-semibold text-amber-700">
            Trial mode is currently ON. Agents are not receiving a copy of what is sent.
        </div>
    @endif

    {{-- SETTINGS FORM --}}
    <form method="POST" action="{{ route('admin.settingsSave') }}"
          class="mt-6 rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]">
        @csrf

        <div class="divide-y divide-slate-100">
            @foreach($definitions as $key => $definition)
                <div class="flex flex-wrap items-start justify-between gap-4 px-5 py-6 sm:px-8">

                    <div class="max-w-2xl">
                        <label for="setting-{{ $key }}" class="text-base font-semibold text-slate-900">
                            {{ $definition['label'] }}
                        </label>

                        <p class="mt-1 text-sm text-slate-600">
                            {{ $definition['description'] }}
                        </p>
                    </div>

                    <div class="shrink-0">
                        @if($definition['type'] === 'toggle')
                            <label class="flex cursor-pointer items-center gap-3 text-sm font-semibold text-slate-700">
                                <input type="checkbox"
                                       id="setting-{{ $key }}"
                                       name="{{ $key }}"
                                       value="1"
                                       class="h-5 w-5 rounded border-slate-300"
                                       @checked(($values[$key] ?? '0') === '1')>
                                On
                            </label>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

        <div class="flex justify-end border-t border-slate-100 px-5 py-4 sm:px-8">
            <button type="submit"
                    class="rounded-lg bg-[#214e9b] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#1b3f80]">
                Save Settings
            </button>
        </div>
    </form>

</div>
</main>

@include('public.layout.footer')

</body>
</html>
