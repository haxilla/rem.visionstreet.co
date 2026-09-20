{{--
    A review-only agent list: used by the "No Start Date + Credits", "No Photo" and
    "No Logo" tabs on the Agents page. No delete checkboxes - each row links to the
    agent to fix things there.

    Needs:
      $agents         a paginator
      $title          heading
      $description    one line under the heading
      $emptyText      shown when the list is empty
      $showStartDate  (bool) show each agent's start date
      $showCredits    (bool) show each agent's credit balance

    Also reachable by URL through the /admin/{segments} convention, where none of
    those exist - hence the guard below.
--}}
@php
    abort_unless(isset($agents, $title), 404);

    $description   = $description ?? '';
    $emptyText     = $emptyText ?? 'No agents found.';
    $showStartDate = $showStartDate ?? false;
    $showCredits   = $showCredits ?? false;
    $columnCount   = 4 + ($showStartDate ? 1 : 0) + ($showCredits ? 1 : 0);
@endphp

<div class="mb-5 flex flex-wrap items-center justify-between gap-2">
    <div>
        <h2 class="text-xl font-semibold text-slate-900">
            {{ $title }}
        </h2>

        @if($description !== '')
            <p class="mt-1 text-sm text-slate-500">
                {{ $description }}
            </p>
        @endif
    </div>

    @if(method_exists($agents, 'total'))
        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
            {{ $agents->total() }} records
        </span>
    @endif
</div>

{{-- MOBILE CARDS --}}
<div class="space-y-3 xl:hidden">

    @forelse($agents as $agent)

        @php
            $displayName = trim(($agent->agtFirst ?? '') . ' ' . ($agent->agtLast ?? ''));

            if ($displayName === '') {
                $displayName = $agent->agtFullName
                    ?: $agent->agtUname
                    ?: $agent->agtEmail
                    ?: 'No Name';
            }
        @endphp

        <div class="rounded-2xl border border-slate-200 p-4">
            <div class="min-w-0">
                <a href="/admin/agentView/{{ $agent->id }}" class="block break-words text-sm font-semibold text-slate-900 hover:underline">
                    {{ $displayName }}
                </a>
                <div class="break-words text-xs text-slate-500">
                    {{ $agent->agtEmail ?: '—' }}
                </div>
            </div>

            @if($showStartDate || $showCredits)
                <div class="mt-2 flex flex-wrap gap-2 text-xs font-semibold">
                    @if($showStartDate)
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                            Started {{ $agent->startDate ? \Carbon\Carbon::parse($agent->startDate)->format('m/d/Y') : '—' }}
                        </span>
                    @endif

                    @if($showCredits)
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700">
                            {{ number_format($agent->remCreds ?? 0) }} {{ ($agent->remCreds ?? 0) == 1 ? 'credit' : 'credits' }}
                        </span>
                    @endif
                </div>
            @endif

            <div class="mt-3 flex items-center justify-between gap-3 border-t border-slate-100 pt-3 text-xs">
                <a href="/admin/agentLogin/{{ $agent->id }}" class="font-semibold text-[#214e9b] hover:underline">
                    ID {{ $agent->id }}
                </a>

                <a href="/admin/agentView/{{ $agent->id }}" class="font-semibold text-[#214e9b] hover:underline">
                    Open agent →
                </a>
            </div>
        </div>

    @empty

        <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
            {{ $emptyText }}
        </div>

    @endforelse

</div>

{{-- TABLE (wide screens) --}}
<div class="hidden overflow-x-auto rounded-2xl border border-slate-200 xl:block">

    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">ID</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Agent</th>
                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Email</th>
                @if($showStartDate)
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Start Date</th>
                @endif
                @if($showCredits)
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Credits</th>
                @endif
                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"></th>
            </tr>
        </thead>

        <tbody class="divide-y divide-slate-100 bg-white">

            @forelse($agents as $agent)

                @php
                    $displayName = trim(($agent->agtFirst ?? '') . ' ' . ($agent->agtLast ?? ''));

                    if ($displayName === '') {
                        $displayName = $agent->agtFullName
                            ?: $agent->agtUname
                            ?: $agent->agtEmail
                            ?: 'No Name';
                    }
                @endphp

                <tr class="hover:bg-slate-50">
                    <td class="whitespace-nowrap px-6 py-3 text-sm">
                        <a href="/admin/agentLogin/{{ $agent->id }}" class="font-semibold text-[#214e9b] hover:underline">
                            {{ $agent->id }}
                        </a>
                    </td>

                    <td class="whitespace-nowrap px-6 py-3 text-sm font-semibold text-slate-900">
                        <a href="/admin/agentView/{{ $agent->id }}" class="hover:underline">
                            {{ $displayName }}
                        </a>
                    </td>

                    <td class="whitespace-nowrap px-6 py-3 text-sm text-slate-700">
                        {{ $agent->agtEmail ?: '—' }}
                    </td>

                    @if($showStartDate)
                        <td class="whitespace-nowrap px-6 py-3 text-sm text-slate-700">
                            {{ $agent->startDate ? \Carbon\Carbon::parse($agent->startDate)->format('m/d/Y') : '—' }}
                        </td>
                    @endif

                    @if($showCredits)
                        <td class="whitespace-nowrap px-6 py-3 text-right text-sm font-semibold text-slate-900">
                            {{ number_format($agent->remCreds ?? 0) }}
                        </td>
                    @endif

                    <td class="whitespace-nowrap px-6 py-3 text-right text-sm">
                        <a href="/admin/agentView/{{ $agent->id }}" class="font-semibold text-[#214e9b] hover:underline">
                            Open agent →
                        </a>
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="{{ $columnCount }}" class="px-6 py-10 text-center text-sm text-slate-500">
                        {{ $emptyText }}
                    </td>
                </tr>

            @endforelse

        </tbody>
    </table>

</div>

@if(method_exists($agents, 'links'))
    <div class="mt-5">
        {{ $agents->links() }}
    </div>
@endif
