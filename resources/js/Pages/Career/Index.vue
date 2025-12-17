<script setup>
import { onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue';
import HeroPage from '@/Components/Section/HeroPage.vue' // Sesuaikan path jika beda

// --- TERIMA DATA DARI DATABASE ---
const props = defineProps({
    pageSetting: Object, // Data Hero (Title, BG)
    values: Array,       // Data Core Values
    jobs: Array          // Data Lowongan Kerja
})

onMounted(() => {
    window.scrollTo(0, 0)
})
</script>

<template>
    <Head title="Karir & Budaya" />

    <MainLayout>
        <div class="career-page">

            <div class="hero-mobile-fix">
                <HeroPage 
                    :title="pageSetting?.hero_title || 'Karir & Budaya'" 
                    :bgImage="pageSetting?.hero_bg_path || 'https://images.unsplash.com/photo-1521737711867-e3b97375f902?q=80&w=2000&auto=format&fit=crop'" 
                />
            </div>

            <section class="intro-section py-5">
                <div class="container text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <h2 class="section-title mb-3">{{ pageSetting?.career_intro_title || "Bergabung Bersama Keluarga Besar JBB"}}</h2>
                            <p class="text-muted fs-5">
                                {{ pageSetting?.career_intro_desc || 'Kami percaya bahwa SDM adalah aset terbesar perusahaan. Di JBB, kami tidak hanya membangun infrastruktur listrik, tetapi juga membangun masa depan talenta-talenta terbaik bangsa.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="values-section py-5">
                <div class="container">
                    <div class="text-center mb-5">
                        <h3 class="section-heading text-white">{{ pageSetting?.career_values_title || "Nilai Kerja Kami"}}</h3>
                        <p class="text-white-50">{{ pageSetting?.career_values_subtitle || "Pondasi yang membuat kami terus bertumbuh dan dipercaya"}}</p>
                    </div>

                    <div class="row g-4">
                        <div v-for="val in values" :key="val.id" class="col-md-6 col-lg-3">
                            <div class="value-card-ref">
                                <img :src="val.image" class="card-bg-img" :alt="val.title">
                                <div class="card-overlay"></div>
                                <h4 class="card-top-title">{{ val.title }}</h4>
                                <div class="card-bottom-content">
                                    <p class="card-desc">{{ val.description }}</p> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="jobs-section py-5 bg-light">
                <div class="container">
                    <div class="text-center mb-5">
                    <h2 class="section-title">{{ pageSetting?.career_jobs_title || "Posisi Tersedia"}}</h2>
                        <p class="text-muted">{{ pageSetting?.career_jobs_subtitle || "Temukan peran yang sesuai dengan keahlian dan minat Anda"}}</p>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-lg-10">
                            
                            <div v-if="jobs.length > 0">
                                <div v-for="job in jobs" :key="job.id" class="job-card mb-4">
                                    <div class="card-body p-4">
                                        <div class="row align-items-center">
                                            <div class="col-md-8 mb-3 mb-md-0">
                                                <h4 class="job-title">{{ job.title }}</h4>
                                                <div class="job-meta d-flex flex-wrap gap-2 gap-md-3 mb-3">
                                                    <span class="badge bg-blue-light text-blue">
                                                        <i class="bi bi-building me-1"></i> {{ job.department }}
                                                    </span>
                                                    <span class="badge bg-red-light text-red">
                                                        <i class="bi bi-geo-alt me-1"></i> {{ job.location }}
                                                    </span>
                                                    <span class="badge bg-gray-light text-dark">
                                                        <i class="bi bi-clock me-1"></i> {{ job.type }}
                                                    </span>
                                                    <span class="badge bg-gray-light text-dark">
                                                        <i class="bi bi-briefcase me-1"></i> {{ job.experience }}
                                                    </span>
                                                </div>
                                                <p class="job-desc text-muted mb-0">{{ job.description }}</p>
                                            </div>
                                            <div class="col-md-4 text-md-end">
                                                <button class="btn btn-apply rounded-pill w-100 w-md-auto">
                                                    Lamar Sekarang <i class="bi bi-arrow-right ms-1"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="text-center py-5">
                                <div class="text-muted fst-italic">
                                    <i class="bi bi-search display-4 d-block mb-3"></i>
                               Bagian ini ke database     Saat ini belum ada posisi yang tersedia. <br> Silakan cek kembali di lain waktu.
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
/* --- CORE COLORS --- */
.text-blue { color: #002b49; }
.text-red { color: #e63946; }
.bg-light { background-color: #f9f9f9 !important; }
.bg-blue-light { background-color: rgba(0, 43, 73, 0.1); }
.bg-red-light { background-color: rgba(230, 57, 70, 0.1); }
.bg-gray-light { background-color: #e9ecef; }

/* --- TYPOGRAPHY --- */
.section-title { color: #002b49; font-weight: 800; }
.section-heading { font-weight: 800; margin-bottom: 10px; }

/* --- HERO MOBILE FIX (Copied from previous Component) --- */
@media (max-width: 576px) {
    .hero-mobile-fix :deep(h1) {
        font-size: 1.75rem !important;
        line-height: 1.3 !important;
        margin-bottom: 0.5rem !important;
        font-weight: 700;
    }
    .hero-mobile-fix :deep(.p-5) { padding: 2rem 1.5rem !important; }
    .hero-mobile-fix :deep(.fs-4) { font-size: 1rem !important; line-height: 1.4; }
}

/* --- VALUES SECTION --- */
.values-section { background-color: #002b49; padding-bottom: 80px; }
.value-card-ref {
    position: relative; height: 400px; border-radius: 20px;
    overflow: hidden; cursor: pointer; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease;
}
.value-card-ref:hover { transform: translateY(-10px); }
.card-bg-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease; }
.value-card-ref:hover .card-bg-img { transform: scale(1.1); }
.card-overlay {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0.1) 40%, rgba(0, 0, 0, 0.9) 100%);
    z-index: 1;
}
.card-top-title {
    position: absolute; top: 25px; left: 25px; color: #fff;
    font-weight: 700; font-size: 1.5rem; z-index: 2;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5); width: 80%;
}
.card-bottom-content {
    position: absolute; bottom: 0; left: 0; width: 100%; padding: 25px;
    z-index: 2; transform: translateY(20px); opacity: 0.9; transition: all 0.3s ease;
}
.value-card-ref:hover .card-bottom-content { transform: translateY(0); opacity: 1; }
.card-desc { color: #e0e0e0; font-size: 0.95rem; line-height: 1.5; margin: 0; }

/* --- JOB CARDS --- */
.job-card {
    background: #fff; border: 1px solid #eef0f2; border-radius: 12px;
    border-left: 5px solid #002b49; transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
}
.job-card:hover {
    transform: translateX(5px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    border-left-color: #e63946;
}
.job-title { font-weight: 700; color: #333; margin-bottom: 10px; }
.job-meta .badge { font-weight: 500; padding: 8px 12px; font-size: 0.85rem; }
.job-desc { font-size: 0.95rem; line-height: 1.6; }
.btn-apply {
    background-color: #002b49; color: #fff; padding: 10px 30px;
    font-weight: 600; border: none; transition: all 0.3s;
}
.btn-apply:hover {
    background-color: #e63946; transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(230, 57, 70, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .value-card-ref { height: 300px; }
    .job-meta { gap: 5px; }
    .btn-apply { width: 100%; margin-top: 15px; }
}
</style>