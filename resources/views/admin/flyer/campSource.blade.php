{{-- Who a campaign came from, for the admin flyer page. Needs $adminAdded (bool).
     Free / admin-added is marked in violet, the agent's own send in blue. --}}
@php $adminAdded = $adminAdded ?? false; @endphp
@if($adminAdded)
    <span class="ml-1 rounded-full bg-violet-100 px-2.5 py-0.5 align-middle text-xs font-semibold text-violet-700">Admin added &middot; Free</span>
@else
    <span class="ml-1 rounded-full bg-sky-100 px-2.5 py-0.5 align-middle text-xs font-semibold text-sky-700">Agent sent</span>
@endif
