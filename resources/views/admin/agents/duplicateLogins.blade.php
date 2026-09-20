{{--
    "Duplicate Logins" tab on the Agents page.

    The same login email is registered on more than one account. Nothing here changes data:
    it lists each email with what every account holds, so an admin can move the flyers into
    the account the agent really uses and then delete the old one(s).
    Needs: $groups (built in app/admin/agents.php), $total (how many emails are duplicated).
--}}
@php
    $mdy = fn ($d) => $d ? \Carbon\Carbon::parse($d)->format('m/d/Y') : '—';
    $withFlyersToMove = $groups->where('toMove', '>', 0)->count();
@endphp

<div class="mb-5 flex flex-wrap items-center justify-between gap-2">
    <div>
        <h2 class="text-xl font-semibold text-slate-900">Duplicate Logins</h2>

        <p class="mt-1 max-w-3xl text-sm text-slate-500">
            Login emails used by more than one account. For each, move the flyers into the account the agent really
            uses, then delete the old ones. Until then, signing in with that email opens the account with the most flyers.
        </p>
    </div>

    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
        {{ $total }} {{ $total === 1 ? 'email' : 'emails' }}
        @if($withFlyersToMove > 0)
            &middot; {{ $withFlyersToMove }} with flyers to move
        @endif
    </span>
</div>

@forelse($groups as $group)

    <div class="mb-4 overflow-hidden rounded-2xl border border-slate-200">

        <div class="flex flex-wrap items-center justify-between gap-2 bg-slate-50 px-4 py-3">
            <div class="min-w-0 break-all text-sm font-semibold text-slate-900">
                {{ $group['email'] }}
            </div>

            <div class="flex flex-wrap gap-2 text-xs font-semibold">
                <span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-slate-700">
                    {{ $group['accounts']->count() }} accounts
                </span>

                @if($group['toMove'] > 0)
                    <span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-amber-700 ring-1 ring-amber-200">
                        {{ $group['toMove'] }} {{ $group['toMove'] === 1 ? 'flyer' : 'flyers' }} to move
                    </span>
                @else
                    <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-emerald-700 ring-1 ring-emerald-200">
                        Nothing to move
                    </span>
                @endif

                <a href="/admin/agentMerge/{{ $group['accounts']->first()['id'] }}"
                   class="rounded-full bg-[#214e9b] px-3 py-0.5 text-white hover:bg-[#1b3f80]">
                    Move flyers
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Account</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Office</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Flyers</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Last sent</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Credits</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Start date</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @foreach($group['accounts'] as $row)
                        <tr>
                            <td class="px-4 py-3 text-sm">
                                <a href="/admin/agentView/{{ $row['id'] }}" class="font-semibold text-slate-900 hover:underline">
                                    {{ $row['name'] }}
                                </a>
                                <div class="text-xs text-slate-400">ID {{ $row['id'] }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $row['office'] ?: '—' }}</td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-slate-700">{{ $row['flyers'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">{{ $mdy($row['last_sent']) }}</td>
                            <td class="px-4 py-3 text-right text-sm text-slate-600">{{ $row['credits'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">{{ $mdy($row['start']) }}</td>
                            <td class="px-4 py-3 text-xs font-semibold">
                                @if($row['keep'])
                                    <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-emerald-700 ring-1 ring-emerald-200">Likely keep</span>
                                @elseif($row['empty'])
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-slate-600">Empty</span>
                                @elseif($row['flyers'] > 0)
                                    <span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-amber-700 ring-1 ring-amber-200">Has flyers</span>
                                @endif

                                @if($row['blocked'])
                                    <span class="ml-1 rounded-full bg-red-600 px-2.5 py-0.5 text-white">Blocked</span>
                                @endif

                                {{-- Delete only shows for an account with NO flyers (deleted ones count).
                                     If something else would be lost too, say so instead of a button. --}}
                                @if($row['flyers_all'] === 0)
                                    @if($row['can_delete'])
                                        <form method="POST" action="{{ route('admin.agentDeleteDuplicate', $row['id']) }}"
                                              class="mt-1.5"
                                              @if($confirmDelete ?? true) onsubmit="return confirm('Delete account #{{ $row['id'] }} {{ e($row['name']) }}? This cannot be undone.')" @endif>
                                            @csrf
                                            <input type="hidden" name="from" value="tab">
                                            <button type="submit" class="rounded-lg border border-red-200 px-2.5 py-1 text-xs font-semibold text-red-600 hover:bg-red-50">
                                                Delete account
                                            </button>
                                        </form>
                                    @else
                                        <div class="mt-1.5 max-w-[14rem] text-xs font-normal text-slate-500">
                                            Can't delete: it has {{ implode(', ', $row['reasons']) }}.
                                        </div>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

@empty

    <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
        No login email is used by more than one account.
    </div>

@endforelse

@if($groups->isNotEmpty())
    <p class="text-xs text-slate-500">
        "Likely keep" is a suggestion: the account with the most flyers, then the most recent send, then the oldest.
        "Empty" means no flyers, no start date and no credits. Open an account to see its full history.
    </p>
@endif
