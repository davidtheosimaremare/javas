<script setup>
import { Link } from '@inertiajs/vue3'; // Menggunakan Link Inertia
import { ref, computed } from 'vue';
import HeroPage from '../../Components/Section/HeroPage.vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Pagination, Autoplay } from 'swiper/modules';
import MainLayout from '../../Layouts/MainLayout.vue';

defineOptions({ layout: MainLayout }); 

// --- DEFINISI PROPS DINAMIS (DARI ABOUTCONTROLLER) ---
const props = defineProps({
    heroTitle: String,
    heroBg: String,
    aboutContent: { type: Object, default: () => ({ 
        page_title: 'Sekilas Tentang JBB', 
        content_html: '', 
        quote: 'Precision in Engineering, Excellence in Execution',
        section_subtitle: '',
        quote_explanation_text: '', 
        

    }) },
    galleryImages: { type: Array, default: () => [] },
    misiList: { type: Array, default: () => [] }, // Berisi VISI dan MISI
    leadershipTeam: { type: Array, default: () => [] },
    // projectLocations TIDAK DIGUNAKAN DI TEMPLATE INI
});

// --- LOGIC MISI (Filtering Visi & Misi) ---
const visiItem = computed(() => props.misiList.find(i => i.type === 'visi'));
const misiItems = computed(() => props.misiList.filter(i => i.type === 'misi'));

// --- LOGIC LIGHTBOX ---
const isLightboxOpen = ref(false);
const activeIndex = ref(0);

// Mengambil URL gambar aktif dari array prop galleryImages
const activeLightboxImage = computed(() => {
    return props.galleryImages[activeIndex.value]?.image_url;
});

const openLightbox = (index) => {
    activeIndex.value = index;
    isLightboxOpen.value = true;
    document.body.style.overflow = 'hidden';
}

const closeLightbox = () => {
    isLightboxOpen.value = false;
    document.body.style.overflow = 'auto';
}

const nextImage = () => {
    if (activeIndex.value < props.galleryImages.length - 1) {
        activeIndex.value++;
    } else {
        activeIndex.value = 0;
    }
}

const prevImage = () => {
    if (activeIndex.value > 0) {
        activeIndex.value--;
    } else {
        activeIndex.value = props.galleryImages.length - 1;
    }
}
</script>

<template>
    <div>
        <HeroPage :title="props.heroTitle" :bgImage="props.heroBg" />

        <div class="about-page-wrapper">

            <section class="container py-5 px-4">

                <div class="row justify-content-center mb-5">
                    <div class="col-lg-10">
                        <h2 class="section-title text-center mb-4">{{ props.aboutContent.page_title }}</h2>
                        <div class="content-text text-justify" v-html="props.aboutContent.content_html">
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-12">
                        <swiper :slidesPerView="1" :spaceBetween="20" :loop="true" :autoplay="{
                            delay: 3000,
                            disableOnInteraction: false,
                        }" :pagination="{ clickable: true }" :breakpoints="{
                            '640': { slidesPerView: 2, spaceBetween: 20 },
                            '768': { slidesPerView: 3, spaceBetween: 30 },
                            '1024': { slidesPerView: 4, slidesPerView: 4, spaceBetween: 30 },
                        }" :modules="modules" class="jbb-gallery-swiper">
                            
                            <swiper-slide v-for="(img, index) in props.galleryImages" :key="index">
                                <div class="gallery-item" @click="openLightbox(index)">
                                    <img :src="img.image_url" alt="JBB Activity" class="img-fluid shadow-sm">
                                    <div class="overlay">
                                        <i class="bi bi-zoom-in"></i>
                                    </div>
                                </div>
                            </swiper-slide>
                        </swiper>
                    </div>
                </div>

                <div class="row justify-content-center mb-5">
                    <div class="col-lg-10 text-center">
                        <h3 class="section-subtitle mb-3">{{props.aboutContent.section_subtitle}}</h3>
                        <blockquote class="quote-text fst-italic mb-4">
                            "{{ props.aboutContent.quote }}"
                        </blockquote>
                        <p class="text-muted" v-html="props.aboutContent.quote_explanation_text">
                             
                        </p>
                    </div>
                </div>
            </section>

            <section class="vision-mission-section py-5">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-10 col-12">

                            <div class="jbb-card visi-card mb-4">
                                <h3 class="card-heading">Visi</h3>
                                <div class="visi-content d-flex">
                                    <div class="visi-accent-line"></div>
                                    <p class="visi-text">
                                        {{ visiItem?.content || 'Visi belum ditentukan.' }}
                                    </p>
                                </div>
                            </div>

                            <div class="jbb-card misi-card">
                                <h3 class="card-heading">Misi</h3>
                                <div class="misi-list-wrapper">
                                    <div v-for="(item, index) in misiItems" :key="index" class="misi-item">
                                        <div class="misi-number">
                                            <span>{{ index + 1 }}</span>
                                        </div>
                                        <div class="misi-text">
                                            {{ item.content }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

            <section class="leadership-section py-5 bg-light">
                <div class="container">
                    <div class="text-center mb-5">
                        <h2 class="section-title">Kepemimpinan Perusahaan</h2>
                        <p class="text-muted">Tim profesional yang memimpin JBB menuju inovasi dan keunggulan</p>
                    </div>

                    <div class="row g-4 justify-content-center">
                        <div class="col-lg-3 col-md-4 col-sm-6 col-12" v-for="(person, index) in props.leadershipTeam" :key="index">
                            <div class="leader-card text-center h-100">
                                <div class="leader-image-wrapper mx-auto mb-3">
                                    <img :src="person.image_url" :alt="person.name" class="img-fluid rounded-circle">
                                </div>
                                <h5 class="leader-name">{{ person.name }}</h5>
                                <p class="leader-title text-muted">{{ person.title }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <div v-if="isLightboxOpen" class="lightbox-overlay" @click="closeLightbox">

                <button class="nav-btn prev-btn" @click.stop="prevImage">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <img :src="activeLightboxImage" class="lightbox-img" @click.stop>

                <button class="nav-btn next-btn" @click.stop="nextImage">
                    <i class="bi bi-chevron-right"></i>
                </button>

                <button class="close-btn" @click="closeLightbox">&times;</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* --- STYLE SAMA PERSIS DENGAN KODE ASLI ANDA --- */
:root { --jbb-blue: #002b49; --jbb-light-blue: #00529c; }
.section-title { color: #002b49; font-weight: 600; margin-top: 5px; }
.section-subtitle { color: #00529c; font-weight: 700; }
.content-text p { line-height: 1.8; color: #444; font-size: 1.05rem; }
.text-justify { text-align: justify; }
.quote-text { font-size: 1.5rem; color: #002b49; font-weight: 600; }
.jbb-gallery-swiper { width: 100%; padding-bottom: 50px; }
.swiper-slide { height: 250px; }
.gallery-item { position: relative; cursor: grab; overflow: hidden; border-radius: 1rem; width: 100%; height: 100%; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); }
.gallery-item:active { cursor: grabbing; }
.gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.gallery-item:hover img { transform: scale(1.1); }
.overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 43, 73, 0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease; }
.overlay i { color: #fff; font-size: 2rem; }
.gallery-item:hover .overlay { opacity: 1; }
.vision-mission-section { background-color: #f8f9fa; border-radius: 20px 20px 0 0; }
.jbb-card { background: #fff; border-radius: 12px; padding: 40px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05); border: 1px solid rgba(0, 0, 0, 0.03); transition: transform 0.3s ease; }
.jbb-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); }
.card-heading { color: #00529c; font-weight: 700; font-size: 1.8rem; margin-bottom: 30px; }
.visi-content { align-items: stretch; }
.visi-accent-line { width: 6px; background: linear-gradient(to bottom, #9dc41a, #00529c); border-radius: 50px; margin-right: 25px; flex-shrink: 0; }
.visi-text { font-size: 1.25rem; font-weight: 600; color: #333; line-height: 1.6; margin: 0; }
.misi-item { display: flex; align-items: flex-start; padding: 20px 0; border-bottom: 1px dashed #eee; }
.misi-item:last-child { border-bottom: none; }
.misi-number { flex-shrink: 0; width: 40px; height: 40px; background: linear-gradient(135deg, #d32f2f, #7f0000); color: #fff; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem; margin-right: 20px; box-shadow: 0 4px 10px rgba(0, 82, 156, 0.3); }
.misi-text { font-size: 1rem; color: #555; line-height: 1.6; padding-top: 5px; }
.leadership-section { padding-top: 80px; padding-bottom: 80px; background-color: #f0f2f5; }
.leader-card { background: #fff; border-radius: 15px; padding: 25px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08); transition: transform 0.3s ease, box-shadow 0.3s ease; overflow: hidden; border: 1px solid rgba(0, 0, 0, 0.05); }
.leader-card:hover { transform: translateY(-8px); box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12); }
.leader-image-wrapper { width: 150px; height: 150px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #e0e0e0; box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.05); }
.leader-image-wrapper img { width: 100%; height: 100%; object-fit: cover; filter: grayscale(20%); transition: filter 0.3s ease; }
.leader-card:hover .leader-image-wrapper img { filter: grayscale(0%); }
.leader-name { font-size: 1.25rem; font-weight: 600; color: var(--jbb-blue); margin-top: 20px; margin-bottom: 5px; }
.leader-title { font-size: 0.95rem; color: #666; font-weight: 500; }
.project-map-section { background: #fff; overflow: hidden; }
.map-wrapper { position: relative; width: 100%; aspect-ratio: 16/6; background-color: #f8f9fa; border-radius: 20px; padding: 20px; }
.map-image { width: 100%; height: 100%; object-fit: contain; opacity: 0.4; filter: grayscale(100%) brightness(0.8); }
.map-pin-container { position: absolute; transform: translate(-50%, -50%); cursor: pointer; z-index: 10; }
.pin-pulse { position: relative; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; }
.pin-core { width: 12px; height: 12px; background-color: #00529c; border-radius: 50%; z-index: 2; transition: all 0.3s; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); }
.pin-ring { position: absolute; width: 100%; height: 100%; background-color: rgba(0, 82, 156, 0.4); border-radius: 50%; z-index: 1; animation: pulse 2s infinite; }
@keyframes pulse { 0% { transform: scale(1); opacity: 1; } 100% { transform: scale(3); opacity: 0; } }
.pin-pulse.active .pin-core { background-color: #e63946; transform: scale(1.2); }
.pin-pulse.active .pin-ring { background-color: rgba(230, 57, 70, 0.4); }
.project-popup { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(20px); width: 260px; background: #fff; border-radius: 10px; border: none; opacity: 0; visibility: hidden; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 20; pointer-events: none; }
.project-popup::after { content: ''; position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); border-width: 8px 8px 0; border-style: solid; border-color: #fff transparent transparent transparent; }
.project-popup.show { opacity: 1; visibility: visible; transform: translateX(-50%) translateY(0); pointer-events: auto; }
.popup-img-wrapper { position: relative; height: 140px; border-radius: 10px 10px 0 0; overflow: hidden; }
.popup-img-wrapper img { width: 100%; height: 100%; object-fit: cover; }
.popup-category { position: absolute; top: 10px; right: 10px; background: rgba(0, 43, 73, 0.8); color: #fff; font-size: 0.7rem; padding: 3px 10px; border-radius: 20px; font-weight: bold; }
.popup-body { padding: 15px; text-align: center; }
.client-name { font-weight: 700; font-size: 0.95rem; color: #002b49; margin-bottom: 5px; line-height: 1.3; }
.project-name { font-size: 0.85rem; color: #555; margin-bottom: 10px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.location-name { color: #999; font-size: 0.8rem; }
.lightbox-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.92); z-index: 9999; display: flex; align-items: center; justify-content: center; animation: fadeIn 0.3s; }
.lightbox-img { max-width: 80%; max-height: 85vh; border-radius: 4px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.5); }
.close-btn { position: absolute; top: 20px; right: 30px; background: none; border: none; color: #fff; font-size: 3rem; cursor: pointer; z-index: 10001; opacity: 0.8; transition: opacity 0.2s; }
.close-btn:hover { opacity: 1; }
.nav-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255, 255, 255, 0.1); border: none; color: white; width: 50px; height: 50px; border-radius: 50%; font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; z-index: 10001; }
.nav-btn:hover { background: rgba(255, 255, 255, 0.3); }
.prev-btn { left: 20px; }
.next-btn { right: 20px; }
@keyframes fadeIn { 0% { opacity: 0; } 100% { opacity: 1; } }
@media (max-width: 768px) { .text-justify { text-align: left; } .quote-text { font-size: 1.2rem; } .jbb-card { padding: 25px; } .visi-text { font-size: 1.1rem; } .map-wrapper { aspect-ratio: 4/5; overflow-x: auto; } .map-image { width: 250%; max-width: none; transform: translateX(-30%); } .swiper-slide { height: 300px; } .lightbox-img { max-width: 100%; } .prev-btn { left: 5px; } .next-btn { right: 5px; } .leader-image-wrapper { width: 120px; height: 120px; } }
</style>