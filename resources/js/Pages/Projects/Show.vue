<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import HeroPage from '../../Components/Section/HeroPage.vue'
// --- 1. IMPORT MAIN LAYOUT ---
import MainLayout from '../../Layouts/MainLayout.vue';


// --- IMPORT SWIPER ---
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Autoplay, Pagination } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/pagination'

// --- 1. PROPS & CONFIG ---
const props = defineProps({
    project: Object
})

const swiperModules = [Autoplay, Pagination]

// --- 2. SOCIAL SHARE LOGIC ---
const currentUrl = ref('')
const copyStatus = ref('Salin Link')

const shareLinks = computed(() => {
    const url = typeof window !== 'undefined' ? window.location.href : ''
    const text = encodeURIComponent(`Cek proyek ini: ${props.project.title}`)
    const encodedUrl = encodeURIComponent(url)
    
    return {
        whatsapp: `https://wa.me/?text=${text}%20${encodedUrl}`,
        linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`,
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,
    }
})

const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(window.location.href)
        copyStatus.value = 'Tersalin!'
        setTimeout(() => copyStatus.value = 'Salin Link', 2000)
    } catch (err) {
        console.error('Gagal menyalin', err)
    }
}

// --- 3. LIGHTBOX LOGIC ---
const isLightboxOpen = ref(false)
const currentImageIndex = ref(0)

const activeImage = computed(() => {
    if (!props.project.galleries || props.project.galleries.length === 0) return ''
    return props.project.galleries[currentImageIndex.value]?.image_path
})

const openLightbox = (index) => {
    currentImageIndex.value = index
    isLightboxOpen.value = true
    document.body.style.overflow = 'hidden'
    window.addEventListener('keydown', handleKeydown)
}

const closeLightbox = () => {
    isLightboxOpen.value = false
    document.body.style.overflow = 'auto'
    window.removeEventListener('keydown', handleKeydown)
}

const nextImage = () => {
    if (currentImageIndex.value < props.project.galleries.length - 1) {
        currentImageIndex.value++
    } else {
        currentImageIndex.value = 0
    }
}

const prevImage = () => {
    if (currentImageIndex.value > 0) {
        currentImageIndex.value--
    } else {
        currentImageIndex.value = props.project.galleries.length - 1
    }
}

const handleKeydown = (e) => {
    if (e.key === 'ArrowRight') nextImage()
    if (e.key === 'ArrowLeft') prevImage()
    if (e.key === 'Escape') closeLightbox()
}

// --- LIFECYCLE ---
onMounted(() => {
    window.scrollTo(0, 0)
    currentUrl.value = window.location.href
})

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown)
})
</script>

<template>
    <Head :title="project.title" />

    <MainLayout>
        
        <div class="project-detail-page pb-5 mb-5">

            <div class="hero-mobile-fix">
                <HeroPage :title="project.title" :bgImage="project.hero_image" />
            </div>

            <div class="main-content-wrapper">
                <div class="container py-4 py-lg-5">
                    <div class="row gx-lg-5">

                        <div class="col-lg-8 mb-5">
                            <h3 class="content-heading">Project Overview</h3>
                            <p class="text-muted content-text">{{ project.overview }}</p>

                            <div v-if="project.challenge" class="mt-4 mt-lg-5">
                                <h4 class="content-subheading">The Challenge</h4>
                                <p class="text-muted content-text">{{ project.challenge }}</p>
                            </div>

                            <div v-if="project.solution" class="mt-4 mt-lg-5">
                                <h4 class="content-subheading">Our Solution</h4>
                                <p class="text-muted content-text">{{ project.solution }}</p>
                            </div>

                            <div class="d-none d-lg-block mt-5 pt-4 border-top">
                                <h5 class="fw-bold mb-3 text-blue fs-6">Bagikan Proyek Ini:</h5>
                                <div class="d-flex flex-wrap gap-2">
                                    <a :href="shareLinks.whatsapp" target="_blank" class="btn btn-share btn-wa"><i class="bi bi-whatsapp"></i></a>
                                    <a :href="shareLinks.linkedin" target="_blank" class="btn btn-share btn-li"><i class="bi bi-linkedin"></i></a>
                                    <a :href="shareLinks.facebook" target="_blank" class="btn btn-share btn-fb"><i class="bi bi-facebook"></i></a>
                                    <button @click="copyToClipboard" class="btn btn-share btn-copy px-4">
                                        <i class="bi bi-link-45deg me-1"></i> {{ copyStatus }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="project-info-card mb-4">
                                <h4 class="info-title">Informasi Proyek</h4>
                                <ul class="info-list list-unstyled">
                                    <li><span class="label">Klien</span><span class="value fw-bold text-end">{{ project.client }}</span></li>
                                    <li><span class="label">Lokasi</span><span class="value text-end">{{ project.location }}</span></li>
                                    <li><span class="label">Tahun</span><span class="value text-end">{{ project.year }}</span></li>
                                    <li><span class="label">Kategori</span><span class="value text-end">{{ project.category }}</span></li>
                                    <li><span class="label">Lingkup</span><span class="value text-end">{{ project.scope }}</span></li>
                                    <li>
                                        <span class="label">Status</span>
                                        <span class="badge rounded-pill px-3" 
                                            :class="project.status === 'Completed' ? 'bg-success' : 'bg-warning text-dark'">
                                            {{ project.status }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            </div>

                    </div>
                </div>
            </div>

            <section class="gallery-section py-5 bg-light" v-if="project.galleries && project.galleries.length > 0">
                <div class="container">
                    <h3 class="section-title text-center mb-4 mb-md-5">Dokumentasi Lapangan</h3>

                    <div class="row g-2 g-md-4">
                        <div v-for="(img, index) in project.galleries" :key="img.id" class="col-6 col-md-6 col-lg-3">
                            <div class="gallery-card" @click="openLightbox(index)">
                                <img :src="img.image_path" alt="Gallery Image" loading="lazy">
                                <div class="zoom-overlay">
                                    <i class="bi bi-arrows-fullscreen"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="testimonial-section py-5" v-if="project.testimonials && project.testimonials.length > 0">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-10">

                            <div class="testimonial-slider-card">
                                <i class="bi bi-quote quote-icon-bg"></i>
                                <swiper :modules="swiperModules" :slides-per-view="1" :loop="true"
                                    :autoplay="{ delay: 5000, disableOnInteraction: false }"
                                    :pagination="{ clickable: true, el: '.custom-swiper-pagination' }"
                                    class="testimonial-swiper">
                                    
                                    <swiper-slide v-for="item in project.testimonials" :key="item.id">
                                        <div class="row align-items-center position-relative z-2">
                                            <div class="col-md-3 text-center mb-4 mb-md-0">
                                                <div class="client-avatar-wrapper">
                                                    <img :src="item.avatar" :alt="item.name" class="client-avatar">
                                                    <div class="quote-badge"><i class="bi bi-chat-quote-fill"></i></div>
                                                </div>
                                            </div>
                                            <div class="col-md-9 text-center text-md-start">
                                                <blockquote class="blockquote mb-4">
                                                    <p class="testimonial-text">"{{ item.quote }}"</p>
                                                </blockquote>
                                                <div class="client-info">
                                                    <h5 class="client-name mb-0">{{ item.name }}</h5>
                                                    <small class="client-role text-white-50">
                                                        {{ item.position }} - {{ project.client }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </swiper-slide>
                                </swiper>
                                <div class="custom-swiper-pagination mt-4 d-flex justify-content-center gap-2"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

            <div v-if="isLightboxOpen" class="lightbox-overlay" @click="closeLightbox">
                <button class="close-btn" @click="closeLightbox">&times;</button>
                <button class="nav-btn prev" @click.stop="prevImage"><i class="bi bi-chevron-left"></i></button>
                <img :src="activeImage" class="lightbox-img" @click.stop>
                <button class="nav-btn next" @click.stop="nextImage"><i class="bi bi-chevron-right"></i></button>
                <div class="image-counter" v-if="project.galleries">
                    {{ currentImageIndex + 1 }} / {{ project.galleries.length }}
                </div>
            </div>

            <div class="mobile-share-bar d-flex d-lg-none align-items-center justify-content-between px-4 py-3 shadow-lg">
                <span class="fw-bold text-dark small">Bagikan Proyek:</span>
                <div class="d-flex gap-3">
                    <a :href="shareLinks.whatsapp" target="_blank" class="share-icon-mobile text-success"><i class="bi bi-whatsapp"></i></a>
                    <a :href="shareLinks.linkedin" target="_blank" class="share-icon-mobile text-primary"><i class="bi bi-linkedin"></i></a>
                    <a :href="shareLinks.facebook" target="_blank" class="share-icon-mobile text-blue"><i class="bi bi-facebook"></i></a>
                    <button @click="copyToClipboard" class="share-icon-mobile border-0 bg-transparent text-secondary"><i class="bi bi-link-45deg"></i></button>
                </div>
            </div>

        </div>
    
    </MainLayout>
</template>

<style scoped>
/* --- CORE COLORS --- */
.text-blue { color: #002b49; }
.bg-light { background-color: #f8f9fa !important; }

/* ==================================================================
   FIX HERO TITLE MOBILE (AGRESIF)
   ==================================================================
*/
@media (max-width: 576px) {
    .hero-mobile-fix :deep(h1) {
        font-size: 1.5rem !important; 
        line-height: 1.3 !important;
        margin-bottom: 0.5rem !important;
        font-weight: 800;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }
    .hero-mobile-fix :deep(.p-5) {
        padding: 2rem 1.5rem !important;
    }
    .hero-mobile-fix :deep(.fs-4) {
        font-size: 0.9rem !important;
        line-height: 1.4;
    }
}

/* --- CONTENT --- */
.content-heading { color: #002b49; font-weight: 700; margin-bottom: 20px; font-size: 1.75rem; }
.content-subheading { color: #00529c; font-weight: 600; font-size: 1.3rem; margin-bottom: 10px; }
.content-text { line-height: 1.8; font-size: 1.05rem; text-align: justify; }

/* Mobile Typography */
@media (max-width: 768px) {
    .content-heading { font-size: 1.5rem; }
    .content-subheading { font-size: 1.2rem; }
    .content-text { font-size: 1rem; text-align: left; }
}

/* --- SIDEBAR INFO --- */
.project-info-card {
    background: #fff; padding: 30px; border-radius: 15px; 
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); border-top: 5px solid #002b49;
    /* Sticky di desktop */
    position: sticky; top: 100px; z-index: 10;
}
.info-list li { display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px dashed #eee; }
.info-list li:last-child { border-bottom: none; margin-bottom: 0; }
.info-list .label { color: #888; font-size: 0.9rem; }
.info-list .value { color: #333; font-weight: 500; text-align: right; max-width: 60%; word-break: break-word; }

/* --- SHARE BUTTONS --- */
.btn-share {
    width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.2rem; transition: transform 0.2s; border: none;
}
.btn-share:hover { transform: translateY(-3px); color: #fff; }
.btn-wa { background-color: #25D366; }
.btn-li { background-color: #0077b5; }
.btn-fb { background-color: #1877F2; }
.btn-copy { background-color: #fff; border: 1px solid #ddd; color: #555; width: auto; height: 45px; border-radius: 22.5px; font-size: 0.9rem; font-weight: 600; padding: 0 20px; }
.btn-copy:hover { background-color: #eee; color: #333; transform: none; }

/* --- MOBILE FIXED SHARE BAR --- */
.mobile-share-bar {
    position: fixed; bottom: 0; left: 0; width: 100%;
    background: #fff; border-top: 1px solid #eee; z-index: 9999;
    padding-bottom: env(safe-area-inset-bottom);
}
.share-icon-mobile { font-size: 1.5rem; text-decoration: none; transition: transform 0.2s; }
.share-icon-mobile:active { transform: scale(0.9); }

/* --- GALLERY --- */
.gallery-card { position: relative; aspect-ratio: 1/1; border-radius: 12px; overflow: hidden; cursor: pointer; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); }
.gallery-card img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.gallery-card:hover img { transform: scale(1.1); }
.zoom-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 43, 73, 0.4); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; }
.gallery-card:hover .zoom-overlay { opacity: 1; }
.zoom-overlay i { color: #fff; font-size: 2rem; }

/* --- TESTIMONIAL --- */
.testimonial-section { background: #fff; }
.testimonial-slider-card { background: linear-gradient(135deg, #e63946 0%, #b71c1c 100%); color: #fff; border-radius: 20px; padding: 50px; position: relative; overflow: hidden; box-shadow: 0 15px 40px rgba(230, 57, 70, 0.3); }
@media (max-width: 768px) {
    .testimonial-slider-card { padding: 30px 20px; text-align: center; }
    .quote-icon-bg { font-size: 6rem; top: -10px; right: 10px; opacity: 0.1; }
}
.quote-icon-bg { position: absolute; top: -20px; right: 20px; font-size: 10rem; color: rgba(255, 255, 255, 0.08); pointer-events: none; z-index: 0; }
.client-avatar-wrapper { position: relative; display: inline-block; }
.client-avatar { width: 100px; height: 100px; border-radius: 50%; border: 4px solid rgba(255, 255, 255, 0.3); object-fit: cover; }
.quote-badge { position: absolute; bottom: 0; right: 0; background: #fff; color: #e63946; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; border: 2px solid #e63946; }
.testimonial-text { font-size: 1.2rem; font-style: italic; line-height: 1.6; font-weight: 300; opacity: 0.95; }
.client-name { font-weight: 700; letter-spacing: 0.5px; }
.custom-swiper-pagination { z-index: 10; position: relative; }
:deep(.swiper-pagination-bullet) { background: rgba(255, 255, 255, 0.5); width: 10px; height: 10px; opacity: 1; }
:deep(.swiper-pagination-bullet-active) { background: #fff; width: 30px; border-radius: 5px; }

/* --- LIGHTBOX (MOBILE OPTIMIZED) --- */
.lightbox-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.95); z-index: 10000; display: flex; align-items: center; justify-content: center; }
.lightbox-img { max-width: 95%; max-height: 80vh; border-radius: 5px; box-shadow: 0 0 50px rgba(0, 0, 0, 0.5); animation: fadeIn 0.3s; }
.close-btn { position: absolute; top: 15px; right: 15px; background: rgba(0,0,0,0.5); border-radius: 50%; width: 40px; height: 40px; border: none; color: #fff; font-size: 2rem; cursor: pointer; z-index: 10001; display: flex; align-items: center; justify-content: center; }
.nav-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255, 255, 255, 0.2); border: none; color: #fff; width: 45px; height: 45px; border-radius: 50%; font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10001; }
.nav-btn.prev { left: 10px; }
.nav-btn.next { right: 10px; }
.image-counter { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); color: #fff; background: rgba(0,0,0,0.5); padding: 5px 15px; border-radius: 20px; }
@keyframes fadeIn { from { opacity: 0.5; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
</style>