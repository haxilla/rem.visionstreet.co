import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';

export function initSwipers() {
    document.querySelectorAll('[data-swiper]').forEach((el) => {
        const type = el.dataset.swiper || 'default';

        let config = {
            modules: [Navigation, Pagination, Autoplay],
            navigation: {
                nextEl: el.querySelector('.swiper-button-next'),
                prevEl: el.querySelector('.swiper-button-prev'),
            },
            pagination: {
                el: el.querySelector('.swiper-pagination'),
                clickable: true,
            },
            watchOverflow: true,
        };

        if (type === 'hero') {
            config = {
                ...config,
                loop: true,
                slidesPerView: 1,
                spaceBetween: 20,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                },
            };
        } else if (type === 'top-viewed') {
            config = {
                ...config,
                loop: true,
                slidesPerView: 1,
                spaceBetween: 24,
                autoplay: false,
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 24,
                    },
                    1200: {
                        slidesPerView: 3,
                        spaceBetween: 28,
                    },
                },
            };
        } else {
            config = {
                ...config,
                loop: true,
                slidesPerView: 1,
                spaceBetween: 20,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                },
            };
        }

        new Swiper(el, config);
    });
}