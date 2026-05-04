import Swiper from 'swiper';
import { Navigation, Thumbs, Keyboard } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/thumbs';

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('photoModal');
    if (!modal) return;

    const closeBtn = document.getElementById('photoModalClose');

    let mainSwiper = null;
    let thumbSwiper = null;

    function initSwipers() {
        if (mainSwiper) return;

        thumbSwiper = new Swiper('.photo-modal-thumbs', {
            modules: [Thumbs],
            slidesPerView: 6,
            spaceBetween: 10,
            watchSlidesProgress: true,
        });

        mainSwiper = new Swiper('.photo-modal-main', {
            modules: [Navigation, Thumbs, Keyboard],
            slidesPerView: 1,
            spaceBetween: 20,
            keyboard: {
                enabled: true,
                onlyInViewport: false,
            },
            navigation: {
                nextEl: '.photo-modal-next',
                prevEl: '.photo-modal-prev',
            },
            thumbs: {
                swiper: thumbSwiper,
            },
        });
    }

    function openModal(index) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        initSwipers();

        requestAnimationFrame(() => {
            mainSwiper.update();
            thumbSwiper.update();

            mainSwiper.slideTo(index, 0);
            thumbSwiper.slideTo(index, 0);
        });
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.querySelectorAll('[data-photo-open]').forEach((el) => {
        el.addEventListener('click', () => {
            openModal(parseInt(el.dataset.photoOpen, 10) || 0);
        });
    });

    closeBtn?.addEventListener('click', closeModal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
});