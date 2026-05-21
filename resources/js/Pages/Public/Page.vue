<script setup>
import { Head } from '@inertiajs/vue3'
import Header from '@/Components/Partials/Header.vue'
import Footer from '@/Components/Partials/Footer.vue'

const props = defineProps({
    page: Object
})

const getImgUrl = (path) => {
    if (!path) return '/images/no-image.png';
    if (path.startsWith('http')) return path;
    return path.startsWith('/storage') ? path : `/storage/${path.replace(/^storage\//, '')}`;
}
</script>

<template>
    <Head>
        <title>{{ page.meta_title || page.title }}</title>
        <meta name="description" :content="page.meta_description || `Halaman ${page.title} PT JBB Javas Berkah Bistari`" />
    </Head>

    <Header />

    <main class="page-content" style="min-height: 70vh; padding-top: 100px;">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h1 class="fw-bold text-navy mb-4 text-center">{{ page.title }}</h1>
                    
                    <div v-if="page.hero_image" class="mb-5 rounded-4 overflow-hidden shadow-sm">
                        <img :src="getImgUrl(page.hero_image)" class="w-100 object-fit-cover" style="max-height: 500px;" :alt="page.title">
                    </div>

                    <div class="rich-content ql-editor px-0" v-html="page.content"></div>
                </div>
            </div>
        </div>
    </main>

    <Footer />
</template>

<style scoped>
.text-navy { color: #002b49; }
.object-fit-cover { object-fit: cover; }

/* Styling untuk Rich Text Konten (menyesuaikan Quill Editor public) */
.rich-content :deep(img) {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1rem 0;
}
.rich-content :deep(iframe) {
    width: 100%;
    aspect-ratio: 16/9;
    border-radius: 8px;
    margin: 1rem 0;
}
.rich-content :deep(h1), .rich-content :deep(h2), .rich-content :deep(h3) {
    color: #002b49;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
}
.rich-content :deep(p) {
    line-height: 1.8;
    color: #475569;
    font-size: 1.05rem;
    margin-bottom: 1.5rem;
}
</style>
