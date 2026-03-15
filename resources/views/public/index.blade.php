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
</body>

<div id="member-login-modal" style="display:none;" class="fixed inset-0 z-50 items-center justify-center bg-black/60 p-4">
    <div class="relative w-full max-w-[560px] overflow-hidden rounded-[22px] shadow-[0_30px_80px_rgba(0,0,0,.35)]">
        <button
        type="button"
        class="absolute right-4 top-3 z-10 flex h-8 w-8 items-center justify-center
        rounded-full bg-white/10 text-gray-300 backdrop-blur-sm
        transition-all duration-200 hover:bg-white/20 hover:text-white cursor-pointer"
        onclick="document.getElementById('member-login-modal').style.display='none'">

            <i class="ti-close text-[14px]"></i>

        </button>

        <div class="member-login-modal-content"></div>
    </div>
</div>

</html>