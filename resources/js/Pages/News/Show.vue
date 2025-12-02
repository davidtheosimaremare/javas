<script setup>
import { onMounted, computed, ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'

// --- PENTING: PASTIKAN PATH IMPORT INI SESUAI STRUKTUR FOLDER ANDA ---
// Jika folder Layouts ada di 'resources/js/Layouts', gunakan @/Layouts
import MainLayout from '@/Layouts/MainLayout.vue' 

// Jika HeroPage ada di 'resources/js/Components/Section', gunakan path ini:
import HeroPage from '@/Components/Section/HeroPage.vue' 
// Jika HeroPage ada di 'resources/js/Components', hapus '/Section'

// --- 1. PROPS DARI CONTROLLER ---
const props = defineProps({
    news: Object // Menerima object berita tunggal
})

// --- 2. HELPER FORMAT TANGGAL ---
const formatDate = (dateString) => {
    if (!dateString) return ''
    try {
        const options = { day: 'numeric', month: 'long', year: 'numeric' }
        return new Date(dateString).toLocaleDateString('id-ID', options)
    } catch (e) {
        return dateString // Fallback jika format error
    }
}

// --- 3. SOCIAL SHARE LOGIC (SAFE MODE) ---
const shareLinks = computed(() => {
    // PENCEGAHAN ERROR: Jika news belum ada/null, jangan jalankan logic
    if (!props.news) return {}

    // Pastikan kode berjalan di client-side (Browser)
    const url = typeof window !== 'undefined' ? window.location.href : ''
    const text = encodeURIComponent(props.news.title || 'Berita JBB')
    const encodedUrl = encodeURIComponent(url)
    
    return {
        whatsapp: `https://wa.me/?text=${text}%20${encodedUrl}`,
        facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,
        twitter: `https://twitter.com/intent/tweet?text=${text}&url=${encodedUrl}`,
        linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`,
    }
})

// --- 4. SCROLL TO TOP ---
onMounted(() => {
    window.scrollTo(0, 0)
})
</script>

<template>
    <Head :title="news?.title || 'Detail Berita'" />

    <MainLayout>
        
        <div v-if="news" class="news-detail-page pb-5">

            <div class="hero-mobile-fix">
                <HeroPage 
                    :title="news.category" 
                    :bgImage="news.image" 
                />
            </div>

            <div class="container py-5">
                <div class="row justify-content-center">

                    <div class="col-lg-8 col-md-10">

                        <article class="news-article">
                            
                            <div class="article-header mb-4">
                                <h1 class="article-title">{{ news.title }}</h1>

                                <div class="article-meta d-flex align-items-center mt-3 flex-wrap gap-2">
                                    <span class="category-badge">{{ news.category }}</span>
                                    <span class="separator text-muted">/</span>
                                    <span class="date text-muted">
                                        <i class="bi bi-calendar3 me-1"></i> {{ formatDate(news.published_at) }}
                                    </span>
                                    <span class="separator text-muted">/</span>
                                    <span class="author text-muted">
                                        <i class="bi bi-person me-1"></i> {{ news.author || 'Tim JBB' }}
                                    </span>
                                </div>
                            </div>

                            <div class="article-content text-justify">
                                <div v-html="news.content"></div>
                            </div>

                            <div class="share-section mt-5 pt-4 border-top">
                                <h6 class="fw-bold mb-3 text-blue">Bagikan Artikel Ini</h6>
                                <div class="d-flex gap-3">
                                    <a :href="shareLinks.whatsapp" target="_blank" class="share-icon whatsapp" title="Share to WhatsApp">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                    <a :href="shareLinks.linkedin" target="_blank" class="share-icon linkedin" title="Share to LinkedIn">
                                        <i class="bi bi-linkedin"></i>
                                    </a>
                                    <a :href="shareLinks.facebook" target="_blank" class="share-icon facebook" title="Share to Facebook">
                                        <i class="bi bi-facebook"></i>
                                    </a>
                                    <a :href="shareLinks.twitter" target="_blank" class="share-icon twitter" title="Share to X">
                                        <i class="bi bi-twitter-x"></i>
                                    </a>
                                </div>
                            </div>

                        </article>

                        <div class="mt-5">
                            <Link href="/news" class="btn btn-outline-primary-custom rounded-pill">
                                <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar Berita
                            </Link>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        <div v-else class="text-center py-5 my-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Sedang memuat berita...</p>
            <div class="mt-3">
                <Link href="/news" class="btn btn-sm btn-outline-secondary">Kembali</Link>
            </div>
        </div>

    </MainLayout>
</template>

<style scoped>
/* --- COLORS --- */
.text-blue { color: #002b49; }

/* --- TYPOGRAPHY --- */
.article-title {
    font-weight: 800;
    color: #002b49;
    font-size: 2rem;
    line-height: 1.3;
}

.category-badge {
    color: #e63946;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 0.5px;
}

.article-meta { font-size: 0.9rem; }

/* --- HERO MOBILE FIX (Deep Selector untuk Override Component) --- */
@media (max-width: 576px) {
    .hero-mobile-fix :deep(h1) {
        font-size: 1.75rem !important; /* Judul lebih kecil di HP */
        line-height: 1.3 !important;
        margin-bottom: 0.5rem !important;
    }
    .hero-mobile-fix :deep(.p-5) { 
        padding: 2rem 1.5rem !important; /* Padding lebih tipis */
    }
}

/* --- CONTENT STYLE --- */
.article-content {
    color: #333;
    font-size: 1.1rem;
    line-height: 1.8;
}

/* Styling elemen HTML dari Database (v-html) */
:deep(.article-content p) { margin-bottom: 1.5rem; }
:deep(.article-content h2), :deep(.article-content h3) {
    color: #002b49; font-weight: 700; margin-top: 2rem; margin-bottom: 1rem;
}
:deep(.article-content strong) { color: #002b49; }
:deep(.article-content ul), :deep(.article-content ol) { margin-bottom: 1.5rem; padding-left: 1.5rem; }
:deep(.article-content img) {
    max-width: 100%; height: auto; border-radius: 8px; margin: 1rem 0; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* --- SHARE ICONS --- */
.share-icon {
    width: 45px; height: 45px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; text-decoration: none; transition: all 0.3s;
}
.share-icon:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); }

.facebook { background: #e7f3ff; color: #1877f2; }
.twitter { background: #f0f0f0; color: #000; }
.linkedin { background: #eef6fa; color: #0a66c2; }
.whatsapp { background: #e8f7ef; color: #25d366; }

/* --- BUTTONS --- */
.btn-outline-primary-custom {
    border: 1px solid #002b49; color: #002b49; font-weight: 600; padding: 10px 30px; transition: all 0.3s;
}
.btn-outline-primary-custom:hover { background: #002b49; color: #fff; }

/* Responsive adjustments */
@media (max-width: 768px) {
    .article-title { font-size: 1.5rem; }
    .article-content { font-size: 1rem; text-align: left !important; }
}
</style>