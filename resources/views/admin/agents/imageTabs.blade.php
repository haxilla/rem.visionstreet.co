{{--
    The "No Photo" / "No Logo" tab on the Agents page. It is ONE tab; once opened it divides into two lists:

      File missing     the database NAMES a photo / logo for the agent but the file is not on the server -
                       they think they have one and their flyers show none. Listed FIRST (and opened first
                       when there are any): these are the ones to fix.
      Nothing on file  no photo / logo named in the database at all.

    Needs: $kind ('photo' | 'logo'), $param ('nophoto' | 'nologo' - the tab's query name), $sub ('missing' | 'none'
    - the list showing), $missingCount, $noneAgents (paginator of the nothing-on-file list) and, only while
    the missing list is showing, $missingAgents (a paginator). Built in app/admin/agents.php.
    Also reachable by URL through the /admin/{segments} convention, where none of those exist - hence the guard.
--}}
@php
    abort_unless(isset($kind, $param, $sub, $noneAgents), 404);

    $noun      = $kind === 'logo' ? 'logo' : 'photo';
    $missingCount = $missingCount ?? 0;
    $noneCount = method_exists($noneAgents, 'total') ? $noneAgents->total() : 0;
    $column    = $kind === 'logo' ? 'agtLogo' : 'agtPhoto';

    // one pill per list: the one showing is solid blue; the missing-file one is amber while it has something in it
    $pill = function (bool $on, bool $urgent) {
        if ($on) {
            return 'bg-[#214e9b] text-white';
        }

        return $urgent ? 'bg-amber-100 text-amber-800 hover:bg-amber-200' : 'bg-slate-100 text-slate-700 hover:bg-slate-200';
    };
@endphp

<div class="mb-4 flex flex-wrap gap-2">
    <a href="{{ request()->url() }}?{{ $param }}missing_page=1"
       class="rounded-xl px-4 py-2 text-sm font-semibold {{ $pill($sub === 'missing', $missingCount > 0) }}">
        {{ ucfirst($noun) }} file missing
        <span class="ml-2 rounded-full bg-white/60 px-2 py-0.5 text-xs">{{ number_format($missingCount) }}</span>
    </a>

    <a href="{{ request()->url() }}?{{ $param }}_page=1"
       class="rounded-xl px-4 py-2 text-sm font-semibold {{ $pill($sub === 'none', false) }}">
        Nothing on file
        <span class="ml-2 rounded-full bg-white/60 px-2 py-0.5 text-xs">{{ number_format($noneCount) }}</span>
    </a>
</div>

@if($sub === 'missing')

    @include('admin.agents.agentReviewPanel', [
        'agents'        => $missingAgents ?? collect(),
        'title'         => ucfirst($noun) . ' File Missing',
        'description'   => "The database lists a {$noun} for these agents (all with a start date), but the file isn't on the server - so their flyers show none. Fix these first."
                            . ($kind === 'logo' ? " (A logo is also treated as missing when the agent has no office record, because that is where it is looked for.)" : ''),
        'emptyText'     => "Every {$noun} named in the database is on the server.",
        'showStartDate' => true,
        'showCredits'   => true,
        'fileColumn'    => $column,
    ])

@else

    @include('admin.agents.agentReviewPanel', [
        'agents'        => $noneAgents,
        'title'         => 'No ' . ucfirst($noun) . ' on File',
        'description'   => "Agents with a start date who have no {$noun} listed at all.",
        'emptyText'     => "Every agent with a start date has a {$noun} listed.",
        'showStartDate' => true,
        'showCredits'   => true,
    ])

@endif
