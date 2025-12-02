<script setup>
import { ref, watch } from 'vue'
import { Link, Head, router } from '@inertiajs/vue3' // Pakai router untuk search
import HeroPage from '@/Components/Section/HeroPage.vue'
import MainLayout from '@/Layouts/MainLayout.vue'

// --- 1. PROPS DARI CONTROLLER ---
const props = defineProps({
    news: Object,        // Data Pagination Berita
    pageSetting: Object, // Data Hero
    filters: Object      // Data filter pencarian
})

// --- 2. LOGIC SEARCH ---
const search = ref(props.filters.search || '')

const handleSearch = () => {
    router.get('/news', { search: search.value }, {
        preserveState: true, // Jangan refresh full halaman
        replace: true        // Ganti history URL
    })
}

// --- 3. HELPER FORMAT TANGGAL ---
const formatDate = (dateString) => {
    if (!dateString) return ''
    const options = { day: 'numeric', month: 'short', year: 'numeric' }
    return new Date(dateString).toLocaleDateString('id-ID', options)
}
</script>

<template>
    <Head title="Berita & Artikel" />

    <MainLayout>
        
        <div class="news-page">

            <div class="hero-mobile-fix">
                <HeroPage 
                    :title="pageSetting?.title || 'Berita & Artikel'" 
                    :bgImage="pageSetting?.hero_bg || 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=2070&auto=format&fit=crop'" 
                />
            </div>

            <section class="news-list-section py-5">
                <div class="container">

                    <div class="row mb-5 align-items-end g-3">
                        <div class="col-md-8">
                            <h2 class="section-title mb-2">Kabar Terbaru JBB</h2>
                            <p class="text-muted">Informasi terkini seputar proyek, inovasi, dan aktivitas perusahaan.</p>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group search-bar">
                                <input 
                                    v-model="search" 
                                    @keyup.enter="handleSearch"
                                    type="text" 
                                    class="form-control" 
                                    placeholder="Cari berita..."
                                >
                                <button @click="handleSearch" class="btn btn-primary-custom" type="button">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div v-for="item in news.data" :key="item.id" class="col-md-6 col-lg-3">
                            <div class="news-card h-100">

                                <div class="card-img-wrapper">
                                    <img :src="item.image" :alt="item.title" loading="lazy">
                                </div>

                                <div class="card-body d-flex flex-column">

                                    <div class="meta-info mb-2">
                                        <span class="category">{{ item.category }}</span>
                                        <span class="divider">/</span>
                                        <span class="date">{{ formatDate(item.published_at) }}</span>
                                    </div>

                                    <h5 class="news-title mb-3">
                                        <Link :href="`/news/${item.slug}`">{{ item.title }}</Link>
                                    </h5>

                                    <div class="mt-auto">
                                        <Link :href="`/news/${item.slug}`" class="read-more">
                                            Baca Selengkapnya <i class="bi bi-chevron-right"></i>
                                        </Link>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    <div v-if="news.data.length === 0" class="text-center py-5">
                        <p class="text-muted fst-italic">Tidak ada berita yang ditemukan.</p>
                    </div>

                    <div class="pagination-wrapper mt-5" v-if="news.links.length > 3">
                        <template v-for="(link, key) in news.links" :key="key">
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
                                <span v-else v-html="link.label"></span>
                            </span>
                        </template>
                    </div>

                </div>
            </section>

        </div>
    </MainLayout>
</template>

<style scoped>
/* --- COLORS & TYPOGRAPHY --- */
.text-blue { color: #002b49; }
.section-title { color: #002b49; font-weight: 800; }

/* --- HERO MOBILE FIX --- */
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

/* --- SEARCH BAR --- */
.search-bar .form-control { border-radius: 50px 0 0 50px; border: 1px solid #ddd; padding-left: 20px; }
.search-bar .form-control:focus { box-shadow: none; border-color: #00529c; }
.btn-primary-custom { background-color: #002b49; color: #fff; border-radius: 0 50px 50px 0; border: none; padding: 0 20px; transition: 0.3s; }
.btn-primary-custom:hover { background-color: #e63946; }

/* --- NEWS CARD --- */
.news-card {
    background: #fff; border: 1px solid #eef0f2; border-radius: 12px;
    overflow: hidden; transition: all 0.3s ease; display: flex; flex-direction: column;
}
.news-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08); }
.card-img-wrapper { height: 200px; overflow: hidden; }
.card-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.news-card:hover .card-img-wrapper img { transform: scale(1.05); }
.card-body { padding: 20px; }

/* Meta Info */
.meta-info { font-size: 0.85rem; font-weight: 500; }
.category { color: #e63946; font-weight: 600; }
.divider { margin: 0 5px; color: #ccc; }
.date { color: #999; }

/* Title */
.news-title {
    font-size: 1.05rem; font-weight: 700; line-height: 1.5; color: #333;
    display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
}
.news-title a { text-decoration: none; color: inherit; transition: color 0.3s; }
.news-title a:hover { color: #00529c; }

/* Read More Link */
.read-more {
    display: inline-block; text-decoration: none; color: #999;
    font-size: 0.9rem; font-weight: 600; position: relative; transition: color 0.3s;
}
.read-more::after {
    content: ''; position: absolute; bottom: -2px; left: 0; width: 0%; height: 2px;
    background-color: #00529c; transition: width 0.3s ease;
}
.read-more i { margin-left: 5px; transition: transform 0.3s; }
.news-card:hover .read-more { color: #00529c; }
.news-card:hover .read-more::after { width: 100%; }
.news-card:hover .read-more i { transform: translateX(5px); }

/* --- PAGINATION --- */
.pagination-wrapper { display: flex; justify-content: center; align-items: center; gap: 8px; }
.page-btn {
    width: 40px; height: 40px; border-radius: 50%; border: 1px solid #ddd;
    background: #fff; color: #555; font-weight: 600; display: flex; align-items: center;
    justify-content: center; cursor: pointer; transition: all 0.2s; text-decoration: none;
}
.page-btn:hover:not(.disabled) { border-color: #00529c; color: #00529c; background-color: #f0f8ff; }
.page-btn.active { background: #00529c; border-color: #00529c; color: #fff; }
.page-btn.disabled { opacity: 0.5; cursor: not-allowed; background-color: #f9f9f9; }
</style>