<script setup>
import { Link } from '@inertiajs/vue3'
// Import Swiper Vue.js components
import { Swiper, SwiperSlide } from 'swiper/vue'
// Import Swiper modules
import { Navigation } from 'swiper/modules'

// PENTING: JANGAN import Swiper CSS di sini!
// CSS sudah di-import di Home.vue (parent)

// Props dari controller
defineProps({
    items: {
        type: Array,
        required: true
    }
})

// Konfigurasi Breakpoints (Responsive)
const breakpoints = {
    320: { slidesPerView: 1, spaceBetween: 20 }, // HP
    768: { slidesPerView: 2, spaceBetween: 30 }, // Tablet
    1024: { slidesPerView: 3, spaceBetween: 30 } // Desktop (3 kolom)
}

// Module Navigation
const modules = [Navigation]
</script>

<template>
    <section class="recent-news-section">
        <div class="container">
            <div class="container card-home">
                <div class="title-card mb-20">
                    <h2 class="title">Latest News</h2>
                </div>
                <div class="desc-card w-50 mb-5">
                    <p>Temukan berita dan update terkini seputar proyek, inovasi, dan pencapaian kami.</p>
                </div>
            </div>

            <swiper 
                :modules="modules" 
                :slides-per-view="3" 
                :space-between="30" 
                :breakpoints="breakpoints"
                :navigation="{ 
                    nextEl: '.news-btn-next', 
                    prevEl: '.news-btn-prev' 
                }" 
                class="news-swiper"
            >
                <swiper-slide v-for="item in items" :key="item.id">
                    <div class="news-card">
                        <div class="card-img-top">
                            <img :src="item.image" :alt="item.title" loading="lazy">
                        </div>

                        <div class="card-body">
                            <div class="meta-info">
                                <span class="category">{{ item.category }}</span>
                                <span class="divider">/</span>
                                <span class="date">{{ item.date }}</span>
                            </div>

                            <h5 class="news-title">
                                <Link :href="item.link">{{ item.title }}</Link>
                            </h5>

                            <div class="mt-auto">
                                <Link :href="item.link" class="read-more-link">
                                    Baca Selengkapnya <i class="bi bi-chevron-right"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                </swiper-slide>
            </swiper>

            <div class="controls-area">
                <div class="left-control">
                    <Link href="/news" class="btn-pill-outline">
                        Selengkapnya <i class="bi bi-arrow-up-right"></i>
                    </Link>
                </div>

                <div class="right-control">
                    <button class="nav-circle news-btn-prev">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="nav-circle news-btn-next">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.news-swiper {
    padding: 20px 0;
}

.recent-news-section {
    padding: 60px 0;
    background-color: #f9f9f9;
}

/* --- CARD STYLE --- */
.news-card {
    background: #fff;
    border: 1px solid #eef0f2;
    border-radius: 16px;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

.card-img-top {
    height: 220px;
    overflow: hidden;
}

.card-img-top img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.news-card:hover .card-img-top img {
    transform: scale(1.05);
}

.card-body {
    padding: 24px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

/* --- TYPOGRAPHY --- */
.meta-info {
    font-size: 0.85rem;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.category {
    color: #e63946;
    font-weight: 600;
}

.divider {
    color: #ccc;
}

.date {
    color: #888;
}

.news-title {
    font-size: 1.1rem;
    font-weight: 700;
    line-height: 1.5;
    margin-bottom: 20px;
    color: #222;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news-title a {
    text-decoration: none;
    color: inherit;
    transition: color 0.3s;
}

.news-title a:hover {
    color: #00529c;
}

/* --- READ MORE LINK --- */
.read-more-link {
    text-decoration: none;
    color: #999;
    font-weight: 600;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: color 0.3s;
}

.read-more-link i {
    font-size: 0.8rem;
    transition: transform 0.3s;
}

.news-card:hover .read-more-link {
    color: #00529c;
}

.news-card:hover .read-more-link i {
    transform: translateX(3px);
}

/* --- FOOTER CONTROLS --- */
.controls-area {
    margin-top: 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-pill-outline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 28px;
    border: 1px solid #00529c;
    border-radius: 50px;
    color: #00529c;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-pill-outline:hover {
    background-color: #00529c;
    color: #fff;
}

.right-control {
    display: flex;
    gap: 10px;
}

.nav-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #fff;
    border: 1px solid #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #666;
    transition: all 0.3s;
}

.nav-circle:hover {
    border-color: #00529c;
    color: #00529c;
    background: #f0f8ff;
}

.nav-circle:disabled,
.swiper-button-disabled {
    opacity: 0.5;
    cursor: default;
    border-color: #eee;
    color: #ccc;
}

.mb-20 {
    margin-bottom: 20px;
}

.mb-5 {
    margin-bottom: 1.5rem;
}

.w-50 {
    max-width: 50%;
}

.mt-auto {
    margin-top: auto;
}

@media (max-width: 768px) {
    .w-50 {
        max-width: 100%;
    }
}
</style>