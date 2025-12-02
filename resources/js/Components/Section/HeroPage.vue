<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    bgImage: {
        type: String,
        default: 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop'
    }
})

// Logic Perbaikan Path Gambar (Mencegah Blank/404 Error)
const processedBgImage = computed(() => {
    let url = props.bgImage;
    // Pastikan URL memiliki leading slash (/) jika bukan URL eksternal
    if (url && !url.startsWith('/') && !url.startsWith('http')) {
        url = '/' + url;
    }
    return url;
});

// Computed Style untuk background (Menggabungkan gradien dan URL)
const heroStyle = computed(() => {
    // KODE ANDA: Gradasi dari Biru Tua (#002b49)
    const gradient = `linear-gradient(to right, rgba(0, 43, 73, 1) 0%, rgba(0, 43, 73, 0.8) 30%, rgba(0, 43, 73, 0.4) 60%, rgba(0, 43, 73, 0) 100%)`;
    
    return {
        // Menggunakan quotes tunggal di dalam url() untuk keamanan syntax CSS
        backgroundImage: `${gradient}, url('${processedBgImage.value}')`
    };
});
</script>

<template>
    <div class="hero-page" :style="heroStyle">

        <div class="hero-overlay"></div>

        <div class="container h-100 position-relative px-4">
            <div class="content-wrapper">
                <h1 class="hero-title">{{ title }}</h1>
                <div class="title-underline"></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* --- CSS SAMA PERSIS DENGAN KODE ASLI ANDA --- */
.hero-page {
    position: relative;
    width: 100%;
    height: 400px;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    margin-top: -50px;
}

/* EFEK GRADASI BIRU (KIRI TEBAL, KANAN TRANSPARAN) */
.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    /* Ini hanya sebagai fallback, style utamanya di-bind via Vue */
    background: linear-gradient(to right, rgba(0, 43, 73, 1) 0%, rgba(0, 43, 73, 0.8) 30%, rgba(0, 43, 73, 0.4) 60%, rgba(0, 43, 73, 0) 100%);
    z-index: 1;
}

.content-wrapper {
    position: relative;
    z-index: 2;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding-bottom: 60px;
}

.hero-title {
    color: #fff;
    font-weight: 500;
    font-size: 42px;
    text-transform: capitalize;
    letter-spacing: 1px;
    margin-bottom: 10px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.title-underline {
    width: 80px;
    height: 4px;
    background-color: #fff;
    border-radius: 2px;
}

/* Responsiveness */
@media (max-width: 768px) {
    .hero-page {
        height: 300px;
    }

    .hero-title {
        font-size: 2.5rem;
    }
}
</style>