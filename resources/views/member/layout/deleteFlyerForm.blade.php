{{--
    Delete button for a flyer card on the agent dashboard. A small POST form
    (CSRF-protected) rather than a link, because deleting changes data.
    "contents" lets the button sit directly in the card's flex row as if the
    form weren't there. Needs: $flyer.
--}}
<form method="POST" action="/member/flyer/delete" class="contents"
      onsubmit="return confirm('Delete this flyer? It will be removed from your account.');">
    @csrf
    <input type="hidden" name="flyerId" value="{{ $flyer->id }}">

    <button type="submit"
            class="flyer-btn rounded-xl bg-red-50 px-4 py-2.5 text-xs font-bold text-red-700 ring-1 ring-red-200 hover:bg-red-100">
        Delete
    </button>
</form>
