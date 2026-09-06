document.addEventListener('DOMContentLoaded', function () {

    // Clicking a thumbnail swaps the larger photo next to it - screen/
    // edit mode only. The email version of these same templates links
    // these same elements out to the online flyer instead (see
    // $display=='email' branches in the style3/style5 blade files),
    // since there's no JS in an email client to run this.
    function wireThumbs(thumbSelector, largeId) {
        var large = document.getElementById(largeId);
        if (!large) return;

        document.querySelectorAll(thumbSelector).forEach(function (thumb) {
            thumb.addEventListener('click', function (e) {
                e.preventDefault();
                large.src = thumb.src;
            });
        });
    }

    wireThumbs('.style3ThumbImg', 'largePhoto');
    wireThumbs('.style5ThumbImg', 'style5LargePhoto');

});
