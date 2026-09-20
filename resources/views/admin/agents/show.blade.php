@include('public.layout.head')

<body data-section="admin" class="relative bg-white min-h-screen font-sans text-gray-800">

@include('admin.layout.nav')

@php
    $displayName = trim(($agent->agtFirst ?? '') . ' ' . ($agent->agtLast ?? ''));
    if ($displayName === '') {
        $displayName = $agent->agtFullName ?: $agent->agtUname ?: $agent->agtEmail ?: 'No Name';
    }

    $initials = strtoupper(
        substr(preg_replace('/[^A-Za-z]/', '', (string) $agent->agtFirst), 0, 1)
        . substr(preg_replace('/[^A-Za-z]/', '', (string) $agent->agtLast), 0, 1)
    ) ?: strtoupper(substr($displayName, 0, 1));

    $showExpireDate = in_array((int) $agent->accountType, [2, 3], true);

    $day  = fn ($d) => $d ? \Carbon\Carbon::parse($d)->format('m/d/Y') : null;
    $time = fn ($d) => $d ? \Carbon\Carbon::parse($d)->format('m/d/Y g:ia') : null;

    // The login checks only the hashed `password` column. Older agents may
    // still have just the plain-text `agtPswd` from the previous system
    // (never converted) - the value is never shown, only whether one exists.
    $hasHash   = filled($agent->password);
    $hasLegacy = !$hasHash && filled($agent->agtPswd);

    // loginBlocked is a column added by hand (raw SQL); until it exists the
    // attribute is simply absent from the loaded row.
    $blockAvailable = array_key_exists('loginBlocked', $agent->getAttributes());
    $isBlocked      = $blockAvailable && (int) $agent->loginBlocked === 1;

    // passwordResetAt (added by hand with raw SQL) is when the agent set their new password
    // on this site; empty = still to do, and they're emailed a link at their next sign-in.
    $resetAvailable = array_key_exists('passwordResetAt', $agent->getAttributes());
    $resetDone      = $resetAvailable && filled($agent->passwordResetAt);

    // Only real web links become links (never javascript: and the like).
    $webLink = fn ($u) => (is_string($u) && preg_match('#^https?://#i', $u) && filter_var($u, FILTER_VALIDATE_URL)) ? $u : null;

    // The agent's address IS their office address - where the flyers and the
    // agent's own contact form keep it (agtoffices: officeAddress1, city, state,
    // zip) - so that's the only address shown.
    $office       = $agent->theAgtOffice;
    $officeLine1  = trim((string) ($office->officeAddress1 ?? ''));
    $officeLine2  = trim(
        ($office->officeCity ?? '')
        . ((($office->officeCity ?? '') && ($office->officeState ?? '')) ? ', ' : '')
        . ($office->officeState ?? '') . ' ' . ($office->officeZip ?? '')
    );

    // [label, value, link type]
    $contactRows = [
        ['Email',           $agent->agtEmail,     'mail'],
        ['Main phone',      $agent->agtMainPhone, 'tel'],
        ['Mobile',          $agent->agtMobile,    'tel'],
        ['Home phone',      $agent->agtHomePhone, 'tel'],
        ['Secondary phone', $agent->agtPhone2,    'tel'],
        ['Website',         $agent->agtWebsite,   'web'],
    ];

    $licenseRows = [
        ['MLS ID',             $agent->agtMlsID],
        ['Board',              $agent->agtBoard],
        ['Designations',       $agent->agtDesigs],
        ['County',             $agent->agtCounty],
    ];

    // shared look for the cards
    $card       = 'rounded-[24px] bg-white shadow-[0_12px_35px_rgba(15,23,42,0.06)]';
    $cardHead   = 'border-b border-slate-100 px-5 py-4 sm:px-6';
    $cardBody   = 'px-5 py-2 sm:px-6';
    $row        = 'flex flex-wrap items-center justify-between gap-x-4 gap-y-1 py-3 text-sm';
    $label      = 'text-slate-500';
    $value      = 'break-words text-right font-medium text-slate-900';
    $focusRing  = 'focus:border-[#214e9b] focus:outline-none focus:ring-2 focus:ring-[#214e9b]/20';
@endphp

<main class="min-h-screen bg-[#f4f7fb] pt-24">
<div class="mx-auto max-w-[1400px] px-4 py-6 sm:px-6 lg:px-10 lg:py-8">

    {{-- ===================== PROFILE HEADER ===================== --}}
    <div class="{{ $card }} px-5 py-6 sm:px-8 sm:py-7">

        <div class="text-[12px] font-semibold uppercase tracking-[0.22em] text-[#214e9b]/70">
            Admin / Agents
        </div>

        <div class="mt-4 flex flex-wrap items-start justify-between gap-5">

            <div class="flex min-w-0 items-center gap-4 sm:gap-5">

                @if($photo['url'])
                    <img src="{{ $photo['url'] }}" alt="{{ $displayName }}"
                         class="h-16 w-16 shrink-0 rounded-full object-cover object-top shadow ring-2 ring-white sm:h-20 sm:w-20">
                @else
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-[#214e9b]/10 text-lg font-semibold text-[#214e9b] ring-2 ring-white sm:h-20 sm:w-20 sm:text-xl">
                        {{ $initials }}
                    </div>
                @endif

                <div class="min-w-0">
                    <h1 class="break-words text-2xl font-semibold leading-tight text-slate-900 sm:text-[30px]">
                        {{ $displayName }}
                    </h1>

                    <p class="mt-1 break-all text-sm text-slate-600">
                        Agent ID {{ $agent->id }}
                        <span class="mx-1.5 text-slate-300">&middot;</span>
                        <span class="font-semibold text-slate-900">{{ $agent->xxAgtUname ?: 'No login username' }}</span>
                    </p>

                    {{-- at-a-glance status --}}
                    <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold">
                        @if($agent->startDate)
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700 ring-1 ring-emerald-200">
                                Active since {{ $day($agent->startDate) }}
                            </span>
                        @else
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-700 ring-1 ring-amber-200">
                                No start date
                            </span>
                        @endif

                        @if($isBlocked)
                            <span class="rounded-full bg-red-600 px-3 py-1 uppercase tracking-wide text-white">
                                Login blocked
                            </span>
                        @endif

                        @unless($hasHash)
                            <span class="rounded-full bg-red-50 px-3 py-1 text-red-700 ring-1 ring-red-200">
                                No usable password
                            </span>
                        @endunless

                        @if($resetAvailable && !$resetDone)
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-700 ring-1 ring-amber-200">
                                Hasn't reset password yet
                            </span>
                        @endif

                        @if(!$photo['file'])
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">No photo</span>
                        @endif

                        @if(!$logo['file'])
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">No logo</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ session('admin_agents_list_url', '/admin/agents') }}" class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                    Back to Agents
                </a>
                <a href="/admin/agentFlyerCreate/{{ $agent->id }}" class="rounded-lg border border-[#214e9b] px-4 py-2.5 text-sm font-semibold text-[#214e9b] hover:bg-[#214e9b]/5">
                    Create Flyer
                </a>
                <a href="/admin/agentLogin/{{ $agent->id }}" class="rounded-lg bg-[#16213e] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#22315a]">
                    Log in as this agent
                </a>

                {{-- The ONLY delete on an agent's page, and only for an agent who never started
                     (no start date) and has no credits - the same rule as the "No Start Date" list.
                     (A duplicate account is deleted from the Duplicate Logins tools instead.) --}}
                @if(is_null($agent->startDate) && (int) ($agent->remCreds ?? 0) <= 0)
                    @if(\App\Support\DuplicateAccounts::canDelete($deleteBlockers))
                        <form method="POST" action="{{ route('admin.agentDelete', $agent->id) }}"
                              @if(\App\Models\Core\AdminSetting::confirmAgentDeletion())
                                  onsubmit="return confirm({{ \Illuminate\Support\Js::from('Delete the account for ' . $displayName . ' (ID ' . $agent->id . ')? This cannot be undone.') }})"
                              @endif>
                            @csrf
                            <button type="submit" class="rounded-lg border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50">
                                Delete account
                            </button>
                        </form>
                    @else
                        {{-- Flagged: it has something that deleting would orphan or destroy, so there is no button --}}
                        <div class="w-full text-xs font-semibold text-amber-700 sm:text-right">
                            Can't be deleted: it still has {{ \App\Support\DuplicateAccounts::describe($deleteBlockers) }}.
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>

    @if(session('status'))
        <div class="mt-6 rounded-2xl border border-emerald-300 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-semibold text-red-700">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- ===================== AT A GLANCE ===================== --}}
    <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4">

        <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Flyers Created</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900 sm:text-3xl">{{ number_format($flyerCount) }}</div>
        </div>

        <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Campaigns Sent</div>

            {{-- the count links to every one of this agent's campaigns, grouped by flyer --}}
            <a href="{{ route('admin.agentCampaigns', $agent->id) }}" class="group mt-2 flex flex-wrap items-baseline gap-x-2" title="See all of this agent's campaigns">
                <span class="text-2xl font-semibold text-[#214e9b] group-hover:underline sm:text-3xl">{{ number_format($campaignCount) }}</span>

                {{-- requested or in progress, not finished - so a zero here is never a mystery --}}
                @if($campaignsInQueue > 0)
                    <span class="text-xs font-semibold text-amber-600">+ {{ number_format($campaignsInQueue) }} in queue / in progress</span>
                @endif
            </a>
        </div>

        <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Remaining Credits</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900 sm:text-3xl">{{ number_format($agent->remCreds ?? 0) }}</div>
        </div>

        <div class="rounded-2xl bg-white p-4 shadow-sm sm:p-5">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Last Login</div>
            <div class="mt-2 text-lg font-semibold text-slate-900 sm:text-2xl">{{ $day($agent->lastLogin) ?? '—' }}</div>
        </div>

    </div>

    {{-- ===================== MAIN: two columns ===================== --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,3fr)_minmax(0,2fr)]">

        {{-- ---------- LEFT: profile ---------- --}}
        <div class="min-w-0 space-y-6">

            {{-- PHOTO & LOGO --}}
            <section id="branding" class="{{ $card }}">

                <div class="{{ $cardHead }}">
                    <h2 class="text-base font-semibold text-slate-900 sm:text-lg">Photo &amp; Logo</h2>
                    <p class="mt-0.5 text-sm text-slate-500">
                        Shown on this agent's flyers. Choose a file to add or change one (JPG, PNG, GIF or WebP, up to 5 MB).
                        Images are resized and compressed automatically.
                    </p>
                </div>

                <div class="divide-y divide-slate-100 px-5 sm:px-6">

                    @foreach([
                        'photo' => ['title' => 'Photo', 'data' => $photo, 'canAdd' => true],
                        'logo'  => ['title' => 'Logo',  'data' => $logo,  'canAdd' => $hasOffice],
                    ] as $kind => $spec)

                        @php $img = $spec['data']; @endphp

                        <div class="flex items-center gap-4 py-4">

                            {{-- Thumbnail at a FIXED size, so an oversized or oddly-shaped upload
                                 can never stretch the card. A photo fills a small square (cropped
                                 from the top, never squashed); a logo is shown whole, inside a
                                 small box. --}}
                            <div class="flex shrink-0 items-center justify-center overflow-hidden rounded-xl bg-slate-50 ring-1 ring-slate-200
                                        {{ $kind === 'photo' ? 'h-24 w-24' : 'h-20 w-32' }}">
                                @if($img['url'])
                                    <img src="{{ $img['url'] }}"
                                         alt="{{ $displayName }} {{ strtolower($spec['title']) }}"
                                         class="{{ $kind === 'photo' ? 'h-full w-full object-cover object-top' : 'max-h-full max-w-full object-contain p-1.5' }}">
                                @else
                                    <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                        @if($kind === 'photo')
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.118a7.5 7.5 0 0115 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.5-1.632z"/>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 19.5h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z"/>
                                        @endif
                                    </svg>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">

                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-semibold text-slate-900">{{ $spec['title'] }}</h3>

                                    @if(!$img['file'])
                                        <span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-amber-200">None</span>
                                    @elseif($img['found'])
                                        <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">On file</span>
                                    @else
                                        <span class="rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-red-700 ring-1 ring-red-200">File missing</span>
                                    @endif
                                </div>

                                <div class="mt-0.5 break-all text-xs text-slate-400">
                                    @if(!$img['file'])
                                        No {{ strtolower($spec['title']) }} on file
                                    @elseif(!$img['found'])
                                        Not found on this server: {{ $img['file'] }}
                                    @else
                                        {{ $img['file'] }}
                                        @if($img['legacy'])
                                            <span class="text-slate-500">(old folder)</span>
                                        @endif
                                    @endif
                                </div>

                                @unless($spec['canAdd'])
                                    <p class="mt-1 text-xs font-semibold text-amber-700">
                                        No office record, so a logo can't be added yet.
                                    </p>
                                @endunless

                                {{-- actions: add / change, and clear --}}
                                <div class="mt-2 flex flex-wrap items-center gap-2">

                                    <form method="POST"
                                          action="{{ route('admin.agentImageUpload', [$agent->id, $kind]) }}"
                                          enctype="multipart/form-data"
                                          data-upload-form>
                                        @csrf
                                        <input type="file"
                                               name="image"
                                               id="{{ $kind }}File"
                                               accept="image/jpeg,image/png,image/gif,image/webp"
                                               data-auto-upload
                                               class="sr-only"
                                               @disabled(!$spec['canAdd'])>

                                        <label for="{{ $kind }}File"
                                               class="inline-block rounded-lg px-3 py-1.5 text-xs font-semibold
                                                      {{ $spec['canAdd']
                                                            ? 'cursor-pointer bg-[#214e9b] text-white hover:bg-[#1b3f80]'
                                                            : 'cursor-not-allowed bg-slate-200 text-slate-400' }}">
                                            {{ $img['file'] ? 'Change' : 'Add' }}
                                        </label>
                                    </form>

                                    @if($img['file'])
                                        <form method="POST"
                                              action="{{ route('admin.agentImageClear', [$agent->id, $kind]) }}"
                                              onsubmit="return confirm('Remove this {{ strtolower($spec['title']) }}?');">
                                            @csrf
                                            <button type="submit"
                                                    class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">
                                                Clear
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </div>
                        </div>

                    @endforeach

                </div>
            </section>

            {{-- CONTACT --}}
            <section class="{{ $card }}">

                <div class="{{ $cardHead }}">
                    <h2 class="text-base font-semibold text-slate-900 sm:text-lg">Contact</h2>
                    <p class="mt-0.5 text-sm text-slate-500">How to reach this agent.</p>
                </div>

                <dl class="{{ $cardBody }} divide-y divide-slate-100">
                    @foreach($contactRows as [$rowLabel, $rowValue, $type])
                        <div class="{{ $row }}">
                            <dt class="{{ $label }}">{{ $rowLabel }}</dt>
                            <dd class="{{ $value }}">
                                @if(!$rowValue)
                                    —
                                @elseif($type === 'mail')
                                    <a href="mailto:{{ $rowValue }}" class="text-[#214e9b] hover:underline">{{ $rowValue }}</a>
                                @elseif($type === 'tel')
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $rowValue) }}" class="text-[#214e9b] hover:underline">{{ $rowValue }}</a>
                                @elseif($type === 'web' && $webLink($rowValue))
                                    <a href="{{ $webLink($rowValue) }}" target="_blank" rel="noopener noreferrer" class="text-[#214e9b] hover:underline">{{ $rowValue }}</a>
                                @else
                                    {{ $rowValue }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            {{-- ADDRESS & OFFICE --}}
            <section class="{{ $card }}">

                <div class="{{ $cardHead }}">
                    <h2 class="text-base font-semibold text-slate-900 sm:text-lg">Address &amp; Office</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Where the agent is based and how they're licensed.</p>
                </div>

                <dl class="{{ $cardBody }} divide-y divide-slate-100">

                    <div class="{{ $row }}">
                        <dt class="{{ $label }}">Brokerage</dt>
                        <dd class="{{ $value }}">{{ $office->officeName ?? '—' }}</dd>
                    </div>

                    <div class="{{ $row }}">
                        <dt class="{{ $label }}">Office address</dt>
                        <dd class="{{ $value }}">
                            @if($officeLine1 !== '' || $officeLine2 !== '')
                                {{ $officeLine1 }}
                                @if($officeLine1 !== '' && $officeLine2 !== '')<br>@endif
                                {{ $officeLine2 }}
                            @else
                                —
                            @endif
                        </dd>
                    </div>

                    @foreach($licenseRows as [$rowLabel, $rowValue])
                        <div class="{{ $row }}">
                            <dt class="{{ $label }}">{{ $rowLabel }}</dt>
                            <dd class="{{ $value }}">{{ $rowValue ?: '—' }}</dd>
                        </div>
                    @endforeach

                </dl>
            </section>

        </div>

        {{-- ---------- RIGHT: account + login ---------- --}}
        <div class="min-w-0 space-y-6">

            {{-- ACCOUNT --}}
            <section id="account" class="{{ $card }}">

                <div class="{{ $cardHead }}">
                    <h2 class="text-base font-semibold text-slate-900 sm:text-lg">Account</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Plan, start date and credits.</p>
                </div>

                <dl class="{{ $cardBody }} divide-y divide-slate-100">

                    <div class="{{ $row }}">
                        <dt class="{{ $label }}">Account type</dt>
                        <dd class="{{ $value }}">{{ $agent->accountType ?? '—' }}</dd>
                    </div>

                    {{-- START DATE: editable in place (date picker). Setting one moves the
                         agent from "No Start Date" to "Agents With Start Date". --}}
                    <div class="{{ $row }}">
                        <dt class="{{ $label }}">Start date</dt>
                        <dd class="font-medium text-slate-900">
                            <form method="POST" action="{{ route('admin.agentStartDate', $agent->id) }}"
                                  class="flex flex-wrap items-center justify-end gap-2">
                                @csrf
                                <input type="date"
                                       name="startDate"
                                       required
                                       aria-label="Start date"
                                       value="{{ $agent->startDate ? \Carbon\Carbon::parse($agent->startDate)->format('Y-m-d') : '' }}"
                                       class="rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm {{ $focusRing }}">
                                <button type="submit"
                                        class="rounded-lg bg-[#214e9b] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#1b3f80]">
                                    {{ $agent->startDate ? 'Change' : 'Set' }}
                                </button>
                            </form>
                        </dd>
                    </div>

                    @if($showExpireDate)
                        <div class="{{ $row }}">
                            <dt class="{{ $label }}">Expire date</dt>
                            <dd class="{{ $value }}">{{ $day($agent->expireDate) ?? '—' }}</dd>
                        </div>
                    @endif

                    {{-- REMAINING CREDITS: the current balance in an editable field; change it
                         and Save to set the new balance. Priority credits (pCreds) below
                         is a separate balance and stays read-only here. --}}
                    <div class="{{ $row }}">
                        <dt class="{{ $label }}">Remaining credits</dt>
                        <dd class="font-medium text-slate-900">
                            <form method="POST" action="{{ route('admin.agentCredits', $agent->id) }}"
                                  class="flex flex-wrap items-center justify-end gap-2">
                                @csrf
                                <input type="number"
                                       name="credits"
                                       min="0"
                                       max="100000"
                                       step="1"
                                       required
                                       inputmode="numeric"
                                       aria-label="Remaining credits"
                                       value="{{ $agent->remCreds ?? 0 }}"
                                       class="w-24 rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm {{ $focusRing }}">
                                <button type="submit"
                                        class="rounded-lg bg-[#214e9b] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#1b3f80]">
                                    Save
                                </button>
                            </form>
                        </dd>
                    </div>

                    <div class="{{ $row }}">
                        <dt class="{{ $label }}">Priority credits</dt>
                        <dd class="{{ $value }}">{{ number_format($agent->pCreds ?? 0) }}</dd>
                    </div>

                    <div class="{{ $row }}">
                        <dt class="{{ $label }}">Last login</dt>
                        <dd class="{{ $value }}">{{ $time($agent->lastLogin) ?? '—' }}</dd>
                    </div>

                    <div class="{{ $row }}">
                        <dt class="{{ $label }}">Last known IP</dt>
                        <dd class="{{ $value }}">{{ $agent->IP ?: '—' }}</dd>
                    </div>

                    @if($agent->moved || $agent->removalDate)
                        <div class="{{ $row }}">
                            <dt class="{{ $label }}">Moved / removal date</dt>
                            <dd class="break-words text-right font-medium text-red-600">
                                {{ $day($agent->removalDate) ?? 'Flagged as moved' }}
                            </dd>
                        </div>
                    @endif

                    @if($agent->agtReview || $agent->agtReviewDate)
                        <div class="{{ $row }}">
                            <dt class="{{ $label }}">Admin review</dt>
                            <dd class="{{ $value }}">
                                {{ $agent->agtReview ?: '—' }}
                                @if($agent->agtReviewDate)
                                    ({{ $day($agent->agtReviewDate) }})
                                @endif
                            </dd>
                        </div>
                    @endif

                </dl>
            </section>

            {{-- LOGIN & ACCESS --}}
            <section id="login" class="{{ $card }}">

                <div class="{{ $cardHead }}">
                    <h2 class="text-base font-semibold text-slate-900 sm:text-lg">Login &amp; Access</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Sign-in details and whether this agent can log in.</p>
                </div>

                <dl class="{{ $cardBody }} divide-y divide-slate-100">

                    <div class="{{ $row }}">
                        <dt class="{{ $label }}">Login username</dt>
                        <dd class="break-all text-right font-medium text-slate-900">{{ $agent->xxAgtUname ?: '—' }}</dd>
                    </div>

                    {{-- agtUname is a legacy field; the login username is xxAgtUname --}}
                    <div class="{{ $row }}">
                        <dt class="{{ $label }}">Legacy username</dt>
                        <dd class="break-all text-right font-medium text-slate-900">{{ $agent->agtUname ?: '—' }}</dd>
                    </div>

                    <div class="{{ $row }}">
                        <dt class="{{ $label }}">Password</dt>
                        <dd class="text-right font-medium {{ $hasHash ? 'text-slate-900' : 'text-red-600' }}">
                            @if($hasHash)
                                Set
                            @elseif($hasLegacy)
                                Not converted
                            @else
                                Not set
                            @endif
                        </dd>
                    </div>

                    <div class="{{ $row }}">
                        <dt class="{{ $label }}">New-site password</dt>
                        <dd class="text-right font-medium {{ !$resetAvailable ? 'text-slate-400' : ($resetDone ? 'text-emerald-600' : 'text-amber-600') }}">
                            @if(!$resetAvailable)
                                Not available yet
                            @elseif($resetDone)
                                Reset {{ $time($agent->passwordResetAt) }}
                            @else
                                Not reset yet
                            @endif
                        </dd>
                    </div>

                    <div class="{{ $row }}">
                        <dt class="{{ $label }}">Login access</dt>
                        <dd class="text-right font-medium {{ !$blockAvailable ? 'text-slate-400' : ($isBlocked ? 'text-red-600' : 'text-emerald-600') }}">
                            @if(!$blockAvailable)
                                Not available yet
                            @elseif($isBlocked)
                                Blocked
                            @else
                                Active
                            @endif
                        </dd>
                    </div>

                </dl>

                <div class="space-y-3 px-5 pb-5 sm:px-6 sm:pb-6">

                    @unless($hasHash)
                        <p class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700">
                            @if($hasLegacy)
                                This agent only has an old-system password that was never converted, so they can't
                                sign in until they reset their password.
                            @else
                                This agent has no password, so they can't sign in until they reset it.
                            @endif
                        </p>
                    @endunless

                    @if($isBlocked)
                        <p class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700">
                            Login is blocked. This agent can't sign in, and if they're already signed in they're
                            signed out on their next click.
                        </p>
                    @endif

                    @if($resetAvailable && !$resetDone)
                        <p class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700">
                            This agent hasn't created a password on this site. Their old password can't be used - they
                            create one from the "Create your password" link on the login page, or you can send them the
                            link now.
                        </p>
                    @endif

                    @if(($sameEmailAccounts ?? collect())->isNotEmpty())
                        <div class="rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-600">
                            <p class="font-semibold text-slate-700">
                                {{ $sameEmailAccounts->count() }} other {{ \Illuminate\Support\Str::plural('account', $sameEmailAccounts->count()) }}
                                use this same login email.
                            </p>
                            <p class="mt-0.5">
                                They get one password together, and signing in opens the one with the most flyers.
                                <a href="/admin/agentMerge/{{ $agent->id }}" class="font-semibold text-blue-700 hover:underline">Move flyers between these accounts</a>,
                                or see them all on the
                                <a href="/admin/agents?duplicates=1" class="font-semibold text-blue-700 hover:underline">Duplicate Logins</a>
                                tab.
                            </p>
                            <ul class="mt-1.5 space-y-0.5">
                                @foreach($sameEmailAccounts as $other)
                                    <li>
                                        <a href="{{ route('admin.agentView', $other->id) }}" class="font-semibold text-blue-700 hover:underline">
                                            #{{ $other->id }} {{ $other->agtFullName ?: 'No name' }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @unless($resetAvailable)
                        <p class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700">
                            Password-reset tracking isn't on until the <code>passwordResetAt</code> column has been added
                            to the database.
                        </p>
                    @endunless

                    @unless($blockAvailable)
                        <p class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700">
                            Blocking isn't available until the <code>loginBlocked</code> column has been added to the
                            database.
                        </p>
                    @endunless

                    {{-- The agent has lost access to their login email, so the emailed link can't reach them --}}
                    <details class="rounded-lg border border-slate-200 px-3 py-2 text-sm" @if($errors->has("loginEmail") || $errors->has("new_email")) open @endif>
                        <summary class="cursor-pointer text-xs font-semibold text-slate-700">
                            Agent can't get into their login email? Change it
                        </summary>

                        <form method="POST" action="{{ route('admin.agentLoginEmail', $agent->id) }}" class="mt-3 space-y-3">
                            @csrf

                            <p class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-700">
                                Confirm who this is first (phone call, MLS ID, licence, office). Whoever owns the new
                                email gets this account: its password is cleared, they're emailed a link to create a
                                new one, and the old address is told about the change.
                            </p>

                            <div>
                                <label for="new_email" class="mb-1 block text-xs font-semibold text-slate-600">New login email</label>
                                <input type="email" id="new_email" name="new_email" required maxlength="100"
                                       placeholder="agent@example.com"
                                       value="{{ old('new_email') }}"
                                       class="w-full rounded-lg border border-slate-300 px-2.5 py-1.5 text-sm {{ $focusRing }}">
                            </div>

                            @if(($sameEmailAccounts ?? collect())->isNotEmpty())
                                <label class="flex items-start gap-2 text-xs text-slate-600">
                                    <input type="checkbox" name="all_accounts" value="1" checked class="mt-0.5">
                                    <span>Also move the {{ $sameEmailAccounts->count() }} other {{ \Illuminate\Support\Str::plural('account', $sameEmailAccounts->count()) }} that use this email (recommended - they share one password).</span>
                                </label>
                            @endif

                            <button type="submit"
                                    onclick="return confirm('Change the login email for this agent and send the new address a password link?')"
                                    class="rounded-lg bg-[#214e9b] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#1b3f80]">
                                Change email and send link
                            </button>
                        </form>
                    </details>

                    <div class="flex flex-wrap gap-2 border-t border-slate-100 pt-4">

                        <form method="POST" action="{{ route('admin.agentPasswordReset', $agent->id) }}">
                            @csrf
                            <button type="submit"
                                    class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                Send password reset email
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.agentLoginBlock', $agent->id) }}">
                            @csrf
                            <button type="submit"
                                    @disabled(!$blockAvailable)
                                    class="rounded-lg px-4 py-2.5 text-sm font-semibold disabled:cursor-not-allowed disabled:opacity-40
                                        {{ $isBlocked
                                            ? 'bg-emerald-600 text-white hover:bg-emerald-700'
                                            : 'border border-red-200 text-red-600 hover:bg-red-50' }}">
                                {{ $isBlocked ? 'Unblock login' : 'Block login' }}
                            </button>
                        </form>

                    </div>

                    <p class="text-xs text-slate-500">
                        "Send password reset email" emails the agent a one-time link (good for {{ \App\Support\AgentPasswords::LINK_MINUTES }} minutes) at
                        <span class="break-all font-semibold">{{ $agent->xxAgtUname ?: 'no email on file' }}</span>.
                        The agent chooses their own password; you never see or set it.
                    </p>

                </div>
            </section>

        </div>

    </div>

    {{-- ===================== PURCHASES ===================== --}}
    <section class="mt-6 {{ $card }}">

        <div class="{{ $cardHead }}">
            <h2 class="text-base font-semibold text-slate-900 sm:text-lg">Purchases</h2>
            <p class="mt-0.5 text-sm text-slate-500">Orders placed by this agent.</p>
        </div>

        <div class="p-5 sm:p-6">
            @if($orders->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-10 text-center text-sm text-slate-500">
                    No purchase records found for this agent.
                </div>
            @else
                <div class="overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($orders as $order)
                                <tr class="hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">{{ $order->item_name ?: '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                                        {{ $order->payment_date ? \Carbon\Carbon::parse($order->payment_date)->format('m/d/Y') : '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                                        @if($order->payment_gross !== null)
                                            ${{ number_format($order->payment_gross, 2) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">{{ $order->payment_status ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </section>

</div>
</main>

@include('public.layout.footer')

{{-- Photo / logo: picking a file uploads it straight away. --}}
<script>
document.querySelectorAll('[data-auto-upload]').forEach(function (input) {
    input.addEventListener('change', function () {
        var file = input.files && input.files[0];
        if (!file) return;

        if (file.size > 5 * 1024 * 1024) {
            alert('That image is larger than 5 MB. Please choose a smaller one.');
            input.value = '';
            return;
        }

        input.form.submit();
    });
});
</script>

</body>
</html>
