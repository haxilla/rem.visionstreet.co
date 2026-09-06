document.addEventListener('DOMContentLoaded', function () {

    // Click-to-edit-in-place for the flyer preview text fields
    // (address, city, state, price, MLS#, headline caption). Screen
    // mode only - the .clickable/.editable markup this depends on
    // only exists in the @if($display=='screen') branches of the
    // flyer templates, so none of this can ever appear in an emailed
    // flyer regardless of what this script does.

    function csrfToken(form) {
        var tokenInput = form.querySelector('input[name="_token"]');
        return tokenInput ? tokenInput.value : '';
    }

    function formatForDisplay(fieldName, value) {
        // xCity's static span includes a trailing ", " that isn't
        // part of the value itself.
        if (fieldName === 'xCity') {
            return value + ',';
        }
        return value;
    }

    document.querySelectorAll('.clickable').forEach(function (staticEl) {
        var editEl = staticEl.nextElementSibling;
        if (!editEl || !editEl.classList.contains('editable')) return;

        staticEl.style.cursor = 'pointer';

        staticEl.addEventListener('click', function () {
            staticEl.style.display = 'none';
            // Clear the inline display:none rather than forcing a
            // specific value - editEl is a <span> for most fields but
            // a <div> for xHeadline (styled to fill the whole headline
            // bar), so let each fall back to its own natural display
            // type instead of assuming "inline" fits all of them.
            editEl.style.display = '';

            var input = editEl.querySelector('.flyerFocus');
            if (input) {
                input.focus();
                input.select();
            }
        });
    });

    document.querySelectorAll('.editable').forEach(function (editEl) {
        var form = editEl.querySelector('form');
        var input = editEl.querySelector('.flyerFocus');
        var staticEl = editEl.previousElementSibling;

        if (!form || !input || !staticEl) return;

        var saved = false;

        function closeWithoutSaving() {
            editEl.style.display = 'none';
            staticEl.style.display = '';
        }

        function save() {
            if (saved) return;
            saved = true;

            var flyerIdInput = form.querySelector('input[name="flyerId"]');
            var fieldName = input.name;

            fetch('/member/flyer/save_inline_edit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                },
                body: new URLSearchParams({
                    flyerId: flyerIdInput ? flyerIdInput.value : '',
                    field: fieldName,
                    value: input.value,
                    _token: csrfToken(form),
                }),
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('Save failed: ' + response.status);
                    return response.json();
                })
                .then(function (data) {
                    staticEl.textContent = formatForDisplay(fieldName, data.display);
                })
                .catch(function (err) {
                    console.error('Inline edit save failed:', err);
                })
                .finally(function () {
                    editEl.style.display = 'none';
                    staticEl.style.display = '';
                });
        }

        input.addEventListener('blur', save);

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                input.blur();
            } else if (e.key === 'Escape') {
                saved = true;
                closeWithoutSaving();
            }
        });
    });

});
