import Swiper from 'swiper';
import { Navigation, Thumbs, Keyboard } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/thumbs';

export default function initPhotoModal() {
    const modal = document.getElementById('photoModal');
    if (!modal) return;

    const closeBtn = document.getElementById('photoModalClose');

    const thumbSwiper = new Swiper('.photo-modal-thumbs', {
        modules: [Thumbs],
        slidesPerView: 6,
        spaceBetween: 10,
        watchSlidesProgress: true,
    });

    const mainSwiper = new Swiper('.photo-modal-main', {
        modules: [Navigation, Thumbs, Keyboard],
        slidesPerView: 1,
        navigation: {
            nextEl: '.photo-modal-next',
            prevEl: '.photo-modal-prev',
        },
        keyboard: { enabled: true },
        thumbs: { swiper: thumbSwiper },
    });

    document.querySelectorAll('[data-photo-open]').forEach((el) => {
        el.addEventListener('click', () => {
            const index = parseInt(el.dataset.photoOpen, 10) || 0;

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            mainSwiper.slideTo(index, 0);
            thumbSwiper.slideTo(index, 0);
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    closeBtn?.addEventListener('click', closeModal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
}