{{--
    "Duplicate Names" tab on the Agents page.

    Accounts that share a NAME - the same first and last name, however it is capitalised or punctuated
    (the exact match the agent web address slugs use, so a name listed here is one that would get a number:
    DebraLee, DebraLee2). A shared name is NOT a shared person: two different agents can be called the
    same, so each group shows what points to ONE person (the same email or phone) and what doesn't, and
    every merge / delete from here asks the admin to confirm "same person" (checked again on the server).
    Merging uses the existing merge page with ?by=name; nothing on this tab changes data by itself.
    Needs: $groups (built in app/admin/agents.php), $total (shared names), $likely (groups that look like
    one person twice), $confirmDelete.
--}}
@php
    $mdy = fn ($d) => $d ? \Carbon\Carbon::parse($d)->format('m/d/Y') : '—';
    $showingLikely = request()->boolean('likely');
    $withFlyersToMove = $groups->where('toMove', '>', 0)->count();
@endphp

<div class="mb-5 flex flex-wrap items-center justify-between gap-2">
    <div>
        <h2 class="text-xl font-semibold text-slate-900">Duplicate Names</h2>

        <p class="mt-1 max-w-3xl text-sm text-slate-500">
            Accounts with the same first and last name. Two accounts can be two different people, so check the
            email and phone before merging. The ones marked "same email" or "same phone" are the likely
            duplicates of one person. Merging asks you to confirm they are the same person.
        </p>
    </div>

    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
        <span class="rounded-full bg-blue-100 px-3 py-1 text-blue-700">
            {{ number_format($total) }} {{ $total === 1 ? 'name' : 'names' }} shared
        </span>

        <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700 ring-1 ring-emerald-200">
            {{ number_format($likely) }} likely one person
        </span>

        @if($withFlyersToMove > 0)
            <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-700 ring-1 ring-amber-200">
                {{ number_format($withFlyersToMove) }} with flyers to move
            </span>
        @endif

        @if($showingLikely)
            <a href="/admin/agents?duplicateNames=1" class="rounded-full border border-slate-300 px-3 py-1 text-slate-700 hover:bg-slate-100">
                Show every shared name
            </a>
        @else
            <a href="/admin/agents?duplicateNames=1&likely=1" class="rounded-full border border-slate-300 px-3 py-1 text-slate-700 hover:bg-slate-100">
                Only likely one person
            </a>
        @endif
    </div>
</div>

@forelse($groups as $group)

    <div id="{{ $group['anchor'] }}" class="ag-group mb-4 overflow-hidden rounded-2xl border {{ $group['likely'] ? 'border-emerald-200' : 'border-slate-200' }}">

        <div class="flex flex-wrap items-center justify-between gap-2 bg-slate-50 px-4 py-3">
            <div class="min-w-0 text-sm font-semibold text-slate-900">
                {{ $group['name'] }}
            </div>

            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                <span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-slate-700">
                    {{ $group['accounts']->count() }} accounts
                </span>

                @if($group['sameEmail'])
                    <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-emerald-700 ring-1 ring-emerald-200">Same email</span>
                @endif
                @if($group['samePhone'])
                    <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-emerald-700 ring-1 ring-emerald-200">Same phone</span>
                @endif
                @if($group['sameOffice'])
                    <span class="rounded-full bg-sky-50 px-2.5 py-0.5 text-sky-700 ring-1 ring-sky-200">Same office</span>
                @endif
                @unless($group['likely'])
                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-slate-600">No email or phone in common</span>
                @endunless

                @if($group['toMove'] > 0)
                    <span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-amber-700 ring-1 ring-amber-200">
                        {{ $group['toMove'] }} {{ $group['toMove'] === 1 ? 'flyer' : 'flyers' }} to move
                    </span>
                @endif

                <a href="/admin/agentMerge/{{ $group['accounts']->first()['id'] }}?by=name"
                   class="rounded-full bg-[#214e9b] px-3 py-0.5 text-white hover:bg-[#1b3f80]">
                    Review &amp; merge
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Account</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Login email</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Contact email</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Phone</th>
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
                            <td class="break-all px-4 py-3 text-sm text-slate-600">{{ $row['login'] ?: '—' }}</td>
                            <td class="break-all px-4 py-3 text-sm text-slate-600">{{ $row['email'] ?: '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-600">{{ $row['phone'] ?: '—' }}</td>
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

                                {{-- Delete only shows for an account with NO flyers (deleted ones count) and nothing else to lose.
                                     Deleting also removes that account's LOGIN, so it always asks first, and the server wants the
                                     "same person" confirmation as well. --}}
                                @if($row['flyers_all'] === 0)
                                    @if($row['can_delete'])
                                        <form method="POST" action="{{ route('admin.agentDeleteDuplicate', $row['id']) }}" class="mt-1.5"
                                              onsubmit="return confirm({{ \Illuminate\Support\Js::from('Delete account #' . $row['id'] . ' ' . $row['name'] . ($row['login'] ? ' (login ' . $row['login'] . ')' : '') . '? Only do this if it is the SAME person as another account here. Their login stops working and this cannot be undone.') }})">
                                            @csrf
                                            <input type="hidden" name="by" value="name">
                                            <input type="hidden" name="confirm_same_person" value="1">
                                            <input type="hidden" name="from" value="tab">
                                            <button type="submit" class="rounded-lg border border-red-200 px-2.5 py-1 text-xs font-semibold text-red-600 hover:bg-red-50">
                                                Delete account
                                            </button>
                                        </form>
                                    @else
                                        <div class="mt-1.5 max-w-[14rem] text-xs font-normal text-slate-500">
                                            Can't delete yet: it has {{ implode(', ', $row['reasons']) }}.
                                            @if(in_array('order history', $row['reasons'], true) || in_array('campaign history', $row['reasons'], true))
                                                <a href="/admin/agentMerge/{{ $row['id'] }}?by=name" class="font-semibold text-blue-700 hover:underline">Move it on the merge page</a>
                                            @endif
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
        @if($showingLikely)
            No shared name has an email or phone number in common.
        @else
            No two accounts share a name.
        @endif
    </div>

@endforelse

@if($groups->isNotEmpty())
    <p class="text-xs text-slate-500">
        "Likely keep" is a suggestion: the account with the most flyers, then the most recent send, then the oldest.
        "Empty" means no flyers, no start date and no credits. Merging moves flyers into one account; an account's
        login email stays with that account, so the agent signs in with the one you keep.
    </p>
@endif
