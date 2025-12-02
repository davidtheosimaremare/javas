<script setup>
import { ref } from 'vue'
import { Link, Head } from '@inertiajs/vue3'
import HeroPage from '@/Components/Section/HeroPage.vue' // Pastikan path import benar
// --- 1. IMPORT MAIN LAYOUT ---
import MainLayout from '@/Layouts/MainLayout.vue';

// --- 2. PROPS DARI CONTROLLER ---
const props = defineProps({
    projects: Object,
    mapProjects: Array,
    pageSetting: Object
})

// --- 3. ASSETS & LOGIC ---
const mapImage = 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/17/Geographical_units_of_Indonesia.svg/1200px-Geographical_units_of_Indonesia.svg.png'
const activeLocationId = ref(null)
const setActiveLocation = (id) => { activeLocationId.value = id }
</script>

<template>
    <Head :title="pageSetting?.title || 'Daftar Proyek'" />

    <MainLayout>
        
        <div class="project-page">

            <div class="hero-mobile-fix">
                <HeroPage 
                    :title="pageSetting?.title || 'Proyek & Referensi'" 
                    :subtitle="pageSetting?.subtitle || 'Portofolio pekerjaan terbaik kami di seluruh Indonesia'"
                    :bgImage="pageSetting?.hero_bg || '/storage/defaults/project-hero.jpg'" 
                />
            </div>

            <section class="project-list-section py-5">
                <div class="container">
                    <div class="section-header mb-5">
                        <h3 class="text-blue fw-bold">Daftar Proyek Terkini</h3>
                        <p class="text-muted">Menampilkan hasil karya profesional JBB Group.</p>
                    </div>

                    <div class="row g-4">
                        <div v-for="item in projects.data" :key="item.id" class="col-md-6 col-lg-4">
                            <div class="project-card h-100">
                                <div class="card-img-wrapper">
                                    <img :src="item.hero_image" :alt="item.title">
                                    <div class="client-badge">{{ item.client }}</div>
                                </div>
                                <div class="card-content">
                                    <h5 class="project-title">{{ item.title }}</h5>
                                    <p class="project-location text-muted mb-3">
                                        <i class="bi bi-geo-alt-fill me-1"></i> {{ item.location }}
                                    </p>
                                    <Link :href="`/projects/${item.slug}`" class="read-more-link">
                                        Selengkapnya <i class="bi bi-arrow-right-short"></i>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pagination-wrapper mt-5 d-flex justify-content-center align-items-center gap-2">
                        <template v-for="(link, key) in projects.links" :key="key">
                            <Link 
                                v-if="link.url" 
                                :href="link.url" 
                                class="page-btn"
                                :class="{ 'active': link.active }"
                            >
                                <span v-if="link.label.includes('Previous')"><i class="bi bi-chevron-left"></i></span>
                                <span v-else-if="link.label.includes('Next')"><i class="bi bi-chevron-right"></i></span>
                                <span v-else v-html="link.label"></span>
                            </Link>
                             <span v-else class="page-btn disabled">
                                <span v-if="link.label.includes('Previous')"><i class="bi bi-chevron-left"></i></span>
                                <span v-else-if="link.label.includes('Next')"><i class="bi bi-chevron-right"></i></span>
                                <span v-else>...</span>
                            </span>
                        </template>
                    </div>

                </div>
            </section>

            <section class="map-section py-5 bg-light">
                <div class="container">
                    <div class="text-center mb-5">
                        <h3 class="text-blue fw-bold">Peta Sebaran Proyek</h3>
                        <p class="text-muted">Jangkauan layanan kami di seluruh Indonesia</p>
                    </div>

                    <div class="map-container-card">
                        <div class="d-block d-md-none text-center mb-2 text-muted fst-italic small">
                            <i class="bi bi-arrows-move"></i> Geser peta untuk melihat area lain
                        </div>

                        <div class="map-scroll-wrapper">
                            <div class="map-aspect-box">
                                <img :src="mapImage" alt="Peta Indonesia" class="map-base-img">

                                <div v-for="loc in mapProjects" :key="loc.id" class="map-pin-wrapper"
                                    :style="{ top: loc.top, left: loc.left }"
                                    @mouseenter="setActiveLocation(loc.id)"
                                    @click="setActiveLocation(loc.id)">
                                    
                                    <div class="pin-pulse" :class="{ active: activeLocationId === loc.id }">
                                        <div class="pin-core"></div>
                                        <div class="pin-ring"></div>
                                    </div>

                                    <div class="map-popup card shadow" :class="{ show: activeLocationId === loc.id }">
                                        <div class="popup-header">
                                            <img :src="loc.image" alt="Project" class="popup-img">
                                        </div>
                                        <div class="popup-body">
                                            <h6 class="fw-bold text-blue mb-1">{{ loc.client }}</h6>
                                            <p class="small text-dark mb-1 lh-sm text-truncate">{{ loc.title }}</p>
                                            <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ loc.area }}</small>
                                            <div class="mt-2">
                                                <Link :href="`/projects/${loc.slug}`" class="btn btn-xs btn-outline-primary py-0" style="font-size: 0.7rem;">Lihat</Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

        </div>
    
    </MainLayout>
</template>

<style scoped>
/* Main Colors */
.text-blue { color: #002b49; }
.bg-light { background-color: #f9f9f9 !important; }

/* ==================================================================
   FIX HERO TITLE MOBILE (AGRESIF)
   Menggunakan :deep() untuk menembus komponen HeroPage.
   ==================================================================
*/
@media (max-width: 576px) {
    /* Judul Proyek */
    .hero-mobile-fix :deep(h1) {
        font-size: 1.75rem !important; /* Ukuran pas di HP (sekitar 28px) */
        line-height: 1.3 !important;
        margin-bottom: 0.5rem !important;
        font-weight: 700;
    }

    /* Padding Container Hero (Agar tidak terlalu tinggi) */
    .hero-mobile-fix :deep(.p-5) {
        padding: 2rem 1.5rem !important; 
    }

    /* Subtitle */
    .hero-mobile-fix :deep(.fs-4) {
        font-size: 1rem !important;
        line-height: 1.4;
    }
}

/* Project Card */
.project-card { background: #fff; border-radius: 12px; overflow: hidden; border: 1px solid #eee; transition: all 0.3s ease; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03); }
.project-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08); }
.card-img-wrapper { position: relative; height: 200px; overflow: hidden; }
.card-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.project-card:hover .card-img-wrapper img { transform: scale(1.05); }
.client-badge { position: absolute; top: 15px; right: 15px; background: rgba(0, 43, 73, 0.85); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
.card-content { padding: 20px; }
.project-title { font-weight: 700; font-size: 1.1rem; color: #333; margin-bottom: 8px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.read-more-link { text-decoration: none; color: #00529c; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 5px; transition: gap 0.3s; }
.read-more-link:hover { gap: 8px; }

/* Pagination */
.page-btn { width: 40px; height: 40px; border-radius: 8px; border: 1px solid #ddd; background: #fff; color: #555; font-weight: 600; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
.page-btn:hover:not(.disabled) { background: #f0f8ff; border-color: #00529c; color: #00529c; }
.page-btn.active { background: #00529c; border-color: #00529c; color: #fff; text-decoration: none; }
.page-btn.disabled { opacity: 0.5; cursor: not-allowed; background: #f5f5f5; color: #ccc; pointer-events: none; }

/* Map Section */
.map-container-card { background: #fff; border-radius: 20px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); overflow: hidden; }
.map-scroll-wrapper { width: 100%; overflow-x: auto; overflow-y: hidden; -webkit-overflow-scrolling: touch; position: relative; }
.map-aspect-box { position: relative; width: 100%; min-width: 1000px; aspect-ratio: 2000/857; }
@media (min-width: 992px) { .map-aspect-box { min-width: 100%; } }
.map-base-img { width: 100%; height: 100%; object-fit: contain; opacity: 0.6; filter: grayscale(100%) contrast(1.1); }
.map-pin-wrapper { position: absolute; transform: translate(-50%, -50%); z-index: 10; cursor: pointer; }
.pin-pulse { position: relative; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; }
.pin-core { width: 12px; height: 12px; background: #00529c; border: 2px solid #fff; border-radius: 50%; z-index: 2; transition: all 0.3s; box-shadow: 0 2px 4px rgba(0,0,0,0.3); }
.pin-ring { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 82, 156, 0.3); border-radius: 50%; animation: pulse 2s infinite; }
.pin-pulse.active .pin-core { background: #e63946; transform: scale(1.3); }
.pin-pulse.active .pin-ring { background: rgba(230, 57, 70, 0.3); }
@keyframes pulse { 0% { transform: scale(1); opacity: 1; } 100% { transform: scale(3); opacity: 0; } }
.map-popup { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(10px); width: 220px; background: #fff; border-radius: 8px; border: none; opacity: 0; visibility: hidden; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); z-index: 100; pointer-events: none; }
.map-popup.show { opacity: 1; visibility: visible; transform: translateX(-50%) translateY(0); pointer-events: auto; }
.map-popup::after { content: ''; position: absolute; bottom: -6px; left: 50%; transform: translateX(-50%); border-width: 6px 6px 0; border-style: solid; border-color: #fff transparent transparent transparent; }
.popup-header { height: 90px; overflow: hidden; border-radius: 8px 8px 0 0; }
.popup-img { width: 100%; height: 100%; object-fit: cover; }
.popup-body { padding: 10px; text-align: center; }
@media (max-width: 768px) { .map-popup { width: 180px; bottom: 25px; } .popup-header { height: 70px; } }
</style>