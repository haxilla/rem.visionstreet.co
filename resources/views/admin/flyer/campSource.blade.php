{{-- Who a campaign came from, for the admin pages. Needs $adminAdded (bool); optional
     $slot (1 or 2 - which of the agent's two picks it was, for an agent's own send).
     Free / admin-added is marked in violet, the agent's own send in blue. --}}
@php
    $adminAdded = $adminAdded ?? false;
    $slot       = $slot ?? null;
@endphp
@if($adminAdded)
    <span class="ml-1 rounded-full bg-violet-100 px-2.5 py-0.5 align-middle text-xs font-semibold text-violet-700">Admin added &middot; Free</span>
@else
    <span class="ml-1 rounded-full bg-sky-100 px-2.5 py-0.5 align-middle text-xs font-semibold text-sky-700">Agent sent@if($slot) &middot; Area {{ $slot }}@endif</span>
@endif
