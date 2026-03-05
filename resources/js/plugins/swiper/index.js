import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';

export function initSwipers() {

    document.querySelectorAll('[data-swiper]').forEach((el) => {

        new Swiper(el, {
            modules: [Navigation, Pagination, Autoplay],

            loop: true,
            slidesPerView: 1,
            spaceBetween: 20,

            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
            },

            pagination: {
                el: el.querySelector('.swiper-pagination'),
                clickable: true,
            },

            navigation: {
                nextEl: el.querySelector('.swiper-button-next'),
                prevEl: el.querySelector('.swiper-button-prev'),
            }
        });

    });

}