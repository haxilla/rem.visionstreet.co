{{-- resources/views/home/hero.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full bg-white text-gray-900">
@include('public.layout.head');

<body class="min-h-screen bg-white pt-[50px] linkcheck">
    @include('public.layout.nav')
    <section>
        @include('public.includes.hero_card')
    </section>
    <section>
        @include('public.includes.features_section')
    </section>
    <section>
        @include('public.includes.free_flyer_served_v4')
    </section>
    <section>
        @include('public.includes.top_views_3up_v1')
    </section>
    <!-- Global Modal -->
    <div id="global-modal" style="display:none;" class="fixed inset-0 items-center justify-center bg-black/60 z-50">
        <div class="bg-white rounded-xl shadow-xl w-[480px] max-w-[92%] relative">
            <button
                type="button"
                class="absolute top-3 right-4 text-2xl leading-none"
                onclick="document.getElementById('global-modal').style.display='none'"
            >×</button>

            <div id="global-modal-content"></div>
        </div>
    </div>

</body>
</html>