/*
 * Flyer side panel behaviour for the wizard's Details step.
 *
 * Markup contract (see member/flyer/details.blade.php):
 *   #flyer-side           the panel (right column, or full-screen overlay)
 *   .flyer-stage          clips the scaled flyer
 *   #flyer-scale-wrapper  600px-wide wrapper that gets scaled to fit
 *   #flyer-live           the rendered flyer (its HTML gets swapped)
 *   #openPreviewBtn / #closePreviewBtn   narrow-screen overlay controls
 *
 * Layout and the narrow-screen overlay CSS live in
 * resources/css/components/wizard.css.
 */
(function () {
    'use strict';

    var FLYER_WIDTH = 600;

    function scale() {
        var stage   = document.querySelector('.flyer-stage');
        var wrapper = document.getElementById('flyer-scale-wrapper');

        if (!stage || !wrapper) return;

        var content = wrapper.querySelector('.flyer-panel.active') || wrapper.firstElementChild;
        if (!content) return;

        var available = stage.clientWidth;

        // Hidden (narrow screen, overlay closed) reads as width 0 -
        // scaling to 0 would collapse the flyer, so wait until it's shown.
        if (!available) return;

        var s = Math.min(available / FLYER_WIDTH, 1);

        wrapper.style.transformOrigin = 'top left';
        wrapper.style.transform = 'scale(' + s + ')';
        wrapper.style.height = (content.offsetHeight * s) + 'px';
    }

    function init() {
        var side      = document.getElementById('flyer-side');
        var openBtn   = document.getElementById('openPreviewBtn');
        var closeBtn  = document.getElementById('closePreviewBtn');
        var live      = document.getElementById('flyer-live');
        var wide      = window.matchMedia('(min-width: 1024px)');

        if (!side) return;

        function setOpen(open) {
            side.classList.toggle('is-open', open);
            document.body.style.overflow = open ? 'hidden' : '';

            // Its width is only known once it's actually displayed.
            scale();
        }

        if (openBtn)  openBtn.addEventListener('click', function () { setOpen(true); });
        if (closeBtn) closeBtn.addEventListener('click', function () { setOpen(false); });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && side.classList.contains('is-open')) {
                setOpen(false);
            }
        });

        // Resizing up to a wide screen while the overlay is open: drop the
        // overlay state so it simply becomes the normal side column.
        wide.addEventListener('change', function () { setOpen(false); });

        window.addEventListener('resize', scale);

        // Photos finishing loading (or a refreshed flyer) change the
        // flyer's height after the fact - rescale whenever it does.
        if (live && 'ResizeObserver' in window) {
            new ResizeObserver(scale).observe(live);
        }

        scale();
    }

    window.FlyerSide = { init: init, scale: scale };
})();
