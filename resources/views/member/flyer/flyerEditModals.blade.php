{{-- Shared click-to-edit modals for the flyer preview (screen mode
     only). Rendered once here rather than per style template, since a
     modal overlay doesn't touch the flyer's own layout at all - only
     the trigger elements inside each template need a
     data-modal-trigger attribute pointing at one of these. Values are
     rendered directly from $flyer server-side, so no JS is needed to
     populate them. --}}

<div id="modal-overlay" class="flyer-modal-overlay" style="display:none;">

    {{-- ADDRESS + PRICE (same visual block on the flyer) --}}
    <div class="flyer-modal" id="modal-address" style="display:none;">
        <div class="flyer-modal-header">
            <span>Edit Address &amp; Price</span>
            <button type="button" class="flyer-modal-close" data-modal-close>&times;</button>
        </div>
        <form data-modal-form action="/member/flyer/save_modal_address">
            @csrf
            <input type="hidden" name="flyerId" value="{{ $flyer->id }}">

            <label>Street Address</label>
            <input type="text" name="xFullStreet" value="{{ $flyer->xFullStreet }}">

            <div class="flyer-modal-row">
                <div>
                    <label>City</label>
                    <input type="text" name="xCity" value="{{ $flyer->xCity }}">
                </div>
                <div class="flyer-modal-narrow">
                    <label>State</label>
                    <input type="text" name="xState" value="{{ $flyer->xState }}" maxlength="2">
                </div>
                <div class="flyer-modal-narrow">
                    <label>Zip</label>
                    <input type="text" name="xZip" value="{{ $flyer->xZip ?: $flyer->xxZip }}" maxlength="5">
                </div>
            </div>

            <label>List Price</label>
            <input type="text" name="xListPrice" value="{{ $flyer->xListPrice }}">

            <button type="submit" class="flyer-modal-save">Save</button>
        </form>
    </div>

    {{-- MLS# + HIGHLIGHTS --}}
    <div class="flyer-modal" id="modal-mlsHighlights" style="display:none;">
        <div class="flyer-modal-header">
            <span>Edit MLS# &amp; Highlights</span>
            <button type="button" class="flyer-modal-close" data-modal-close>&times;</button>
        </div>
        <form data-modal-form action="/member/flyer/save_modal_mls_highlights">
            @csrf
            <input type="hidden" name="flyerId" value="{{ $flyer->id }}">

            <label>MLS#</label>
            <input type="text" name="xMlsNum" value="{{ $flyer->xMlsNum }}" placeholder="Leave blank if not in MLS">

            <label>Highlights</label>
            @for ($i = 1; $i <= 8; $i++)
                <input type="text" name="xb{{ $i }}" value="{{ $flyer->theRemarks->{'xb'.$i} ?? '' }}" placeholder="Highlight {{ $i }}">
            @endfor

            <button type="submit" class="flyer-modal-save">Save</button>
        </form>
    </div>

    {{-- REMARKS --}}
    <div class="flyer-modal" id="modal-remarks" style="display:none;">
        <div class="flyer-modal-header">
            <span>Edit Remarks</span>
            <button type="button" class="flyer-modal-close" data-modal-close>&times;</button>
        </div>
        <form data-modal-form action="/member/flyer/save_modal_remarks">
            @csrf
            <input type="hidden" name="flyerId" value="{{ $flyer->id }}">

            <label>Agent Remarks</label>
            <textarea name="xPubRemarks" rows="8">{{ $flyer->theRemarks->xPubRemarks ?? '' }}</textarea>

            <button type="submit" class="flyer-modal-save">Save</button>
        </form>
    </div>

    {{-- HEADLINE GRAPHIC --}}
    <div class="flyer-modal" id="modal-headline" style="display:none;">
        <div class="flyer-modal-header">
            <span>Edit Headline</span>
            <button type="button" class="flyer-modal-close" data-modal-close>&times;</button>
        </div>
        <form data-modal-form action="/member/flyer/save_modal_headline">
            @csrf
            <input type="hidden" name="flyerId" value="{{ $flyer->id }}">

            <label>Headline</label>
            <select name="graphic_words">
                @foreach([
                    'acreage' => 'Acreage', 'agentbonus' => 'Agent Bonus', 'amazingviews' => 'Amazing Views',
                    'backonmarket' => 'Back On Market', 'bankowned' => 'Bank Owned', 'greatbuy' => 'Great Buy',
                    'horseproperty' => 'Horse Property', 'justlisted' => 'Just Listed',
                    'modelcloseout' => 'Model Closeout', 'mustsee' => 'Must See',
                    'openhouse' => 'Open House', 'reduced' => 'Reduced',
                ] as $value => $label)
                    <option value="{{ $value }}" @selected($flyer->theStyle->graphic_words === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <label>Style</label>
            <select name="graphic_style">
                <option value="ul" @selected($flyer->theStyle->graphic_style === 'ul' || !$flyer->theStyle->graphic_style)>Underline</option>
                <option value="bold" @selected($flyer->theStyle->graphic_style === 'bold')>Bold</option>
                <option value="3d" @selected($flyer->theStyle->graphic_style === '3d')>3D</option>
            </select>

            <button type="submit" class="flyer-modal-save">Save</button>
        </form>
    </div>

</div>

<style>
    .flyer-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .5);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .flyer-modal {
        background: #fff;
        border-radius: 16px;
        width: 90%;
        max-width: 420px;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: 0 20px 50px rgba(0,0,0,.25);
    }
    .flyer-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 800;
        font-size: 16px;
        color: #0f172a;
    }
    .flyer-modal-close {
        border: none;
        background: none;
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
        color: #64748b;
    }
    .flyer-modal form {
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .flyer-modal label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
    }
    .flyer-modal input,
    .flyer-modal select,
    .flyer-modal textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 14px;
        box-sizing: border-box;
    }
    .flyer-modal-row {
        display: flex;
        gap: 10px;
    }
    .flyer-modal-row > div {
        flex: 1;
    }
    .flyer-modal-narrow {
        flex: 0 0 70px !important;
    }
    .flyer-modal-save {
        margin-top: 4px;
        border: none;
        border-radius: 10px;
        padding: 10px 18px;
        font-weight: 700;
        color: #fff;
        background: #123f91;
        cursor: pointer;
    }
    .flyer-modal-save:hover {
        background: #0f3274;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    var overlay = document.getElementById('modal-overlay');

    function openModal(name) {
        var modal = document.getElementById('modal-' + name);
        if (!modal) return;

        document.querySelectorAll('.flyer-modal').forEach(function (m) {
            m.style.display = 'none';
        });

        modal.style.display = 'block';
        overlay.style.display = 'flex';
    }

    function closeModal() {
        overlay.style.display = 'none';
    }

    document.querySelectorAll('[data-modal-trigger]').forEach(function (trigger) {
        trigger.style.cursor = 'pointer';
        trigger.addEventListener('click', function () {
            openModal(trigger.dataset.modalTrigger);
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(function (btn) {
        btn.addEventListener('click', closeModal);
    });

    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeModal();
    });

    document.querySelectorAll('[data-modal-form]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var saveBtn = form.querySelector('.flyer-modal-save');
            if (saveBtn) saveBtn.disabled = true;

            fetch(form.getAttribute('action'), {
                method: 'POST',
                body: new FormData(form),
                headers: { 'Accept': 'application/json' },
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('Save failed: ' + response.status);
                    // Reload so the flyer preview reflects the change -
                    // simplest way to stay correct across all 5
                    // differently-structured templates without needing
                    // to know how to patch each one's DOM by hand.
                    window.location.reload();
                })
                .catch(function (err) {
                    console.error('Modal save failed:', err);
                    if (saveBtn) saveBtn.disabled = false;
                });
        });
    });

});
</script>
