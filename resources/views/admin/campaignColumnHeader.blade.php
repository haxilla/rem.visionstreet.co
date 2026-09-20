{{-- Column headings for the dashboard's campaign lists (wide screens only). The column
     widths come from the .camp-wide CSS defined ONCE at the top of admin/dashboard.blade.php
     and shared with the rows, so headings and rows always line up. It is plain CSS (not
     Tailwind classes) so the lists show even before the stylesheet has been rebuilt.
     Needs: $dateLabel (what the last column is: Requested / Started / Finished). --}}
@php $dateLabel = $dateLabel ?? 'Date'; @endphp

<div class="camp-wide border-b border-slate-300 px-3 py-2.5 text-xs font-semibold uppercase tracking-wide text-slate-500">
    <div>Area</div>
    <div>Address</div>
    <div>Agent</div>
    <div class="text-right">Emails</div>
    <div class="text-right">{{ $dateLabel }}</div>
</div>
