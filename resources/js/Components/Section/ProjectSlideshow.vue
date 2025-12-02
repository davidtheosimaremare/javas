<script setup>
import { Link } from '@inertiajs/vue3'; // Ganti RouterLink ke Link Inertia
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

// 1. TERIMA DATA VIA PROPS
// Tidak lagi hardcode 'const slides = ref([...])'
defineProps({
    items: {
        type: Array,
        required: true
    },
    pageSetting: {
        type: Object,
        default: () => ({})
    }
});

const modules = [Navigation, Pagination, Autoplay, EffectFade];
</script>

<template>
    <div>
        <div class="container slideshow">

            <div class="title-slideshow">
                <h2>{{ pageSetting?.project_title || 'Project Reference' }}</h2>
                
                <div class="desc-slideshow w-50">
                    <p>{{ pageSetting?.project_description || 'Portofolio proyek unggulan kami yang menunjukkan dedikasi terhadap kualitas dan ketepatan waktu.' }}</p>
                </div>
            </div>

            <div class="slideshow-hero-wrapper">
                <swiper 
                    :modules="modules" 
                    :slides-per-view="1" 
                    :space-between="0" 
                    :effect="'fade'" 
                    :loop="true"
                    :autoplay="{ delay: 5000, disableOnInteraction: false }"
                    :pagination="{ el: '.custom-pagination', clickable: true }"
                    :navigation="{ nextEl: '.custom-next', prevEl: '.custom-prev' }" 
                    class="mySwiper"
                >
                    <swiper-slide v-for="slide in items" :key="slide.id">
                        <div class="slide-item" :style="{ backgroundImage: `url('${slide.hero_image}')` }">

                            <div class="overlay"></div>

                            <div class="container h-100 position-relative content-wrapper">
                                <div class="slide-text-content">
                                    <h2 class="slide-title">{{ slide.title }}</h2>
                                    <p class="slide-desc">{{ slide.overview }}</p>
                                    
                                    <Link :href="`/projects/${slide.slug}` || '#'" class="btn-selengkapnya">
                                        Lihat Detail <i class="bi bi-arrow-up-right"></i>
                                    </Link>
                                </div>
                            </div>

                        </div>
                    </swiper-slide>

                    <div class="container controls-wrapper">
                        <div class="custom-pagination"></div>
                        <div class="nav-buttons">
                            <button class="nav-btn custom-prev"><i class="bi bi-chevron-left"></i></button>
                            <button class="nav-btn custom-next"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </div>

                </swiper>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* --- STYLE TETAP SAMA PERSIS DENGAN YANG ANDA KIRIM --- */


.title-slideshow {
    margin: 80px 0 40px 0;
}

.desc-slideshow {
    margin-top: 30px;
}

.slideshow-hero-wrapper {
    width: 100%;
    height: 500px;
    border-radius: 20px;
    overflow: hidden;
    position: relative;
    margin-bottom: 50px;
}

.mySwiper {
    width: 100%;
    height: 100%;
}

.slide-item {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    position: relative;
}

.overlay {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: linear-gradient(to right, rgba(23, 28, 48, 0.9) 0%, rgba(23, 28, 48, 0.6) 40%, rgba(23, 28, 48, 0.1) 100%);
    z-index: 1;
}

.content-wrapper {
    z-index: 2;
    display: flex;
    align-items: center;
}

.slide-text-content {
    color: #fff;
    max-width: 600px;
    margin-left: 20px;
}

.slide-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.slide-desc {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.btn-selengkapnya {
    display: inline-block;
    padding: 10px 25px;
    border: 1px solid #fff;
    border-radius: 50px;
    color: #fff;
    text-decoration: none;
    transition: all 0.3s;
    font-weight: bold;
}

.btn-selengkapnya:hover {
    background: #fff;
    color: #000;
}

.controls-wrapper {
    position: absolute;
    bottom: 40px; left: 0; right: 0;
    z-index: 10;
    display: flex;
    justify-content: space-between;
    align-items: center;
    pointer-events: none;
    padding: 0 30px;
}

.custom-pagination {
    pointer-events: auto;
    display: flex;
    gap: 8px;
}

:deep(.swiper-pagination-bullet) {
    width: 10px; height: 10px;
    background: rgba(255, 255, 255, 0.5);
    opacity: 1;
}

:deep(.swiper-pagination-bullet-active) {
    width: 30px;
    border-radius: 5px;
    background: #fff;
}

.nav-buttons {
    pointer-events: auto;
    display: flex;
    gap: 15px;
}

.nav-btn {
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.5);
    color: #fff;
    width: 45px; height: 45px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: 0.3s;
}

.nav-btn:hover {
    background: #fff;
    color: #000;
}

@media (max-width: 768px) {
    .slide-title { font-size: 2rem; }
    .controls-wrapper {
        bottom: 20px;
        flex-direction: column-reverse;
        align-items: flex-start;
        gap: 20px;
    }
    .nav-buttons { align-self: flex-end; }
    .slideshow-hero-wrapper { height: 400px; }
}
</style>