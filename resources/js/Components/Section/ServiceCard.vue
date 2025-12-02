<script setup>
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';

// Definisikan props
const props = defineProps({
    title: { type: String, default: 'Layanan Retail' },
    imageUrl: { type: String, required: true },
    description: { type: String, default: 'Deskripsi layanan...' },
    hoverColor: { type: String, default: '#0047AB' },
    hoverOpacity: { type: [Number, String], default: 0.8 },
    link: { type: String, default: '#' } // Tambahan untuk Inertia
});

const isHovered = ref(false);

const cardStyle = computed(() => {
    // Logika Perbaikan URL (Agar tidak blank putih)
    let url = props.imageUrl;
    if (url && !url.startsWith('/') && !url.startsWith('http')) {
        url = '/' + url;
    }

    return {
        backgroundImage: `
      linear-gradient(to bottom, rgba(0,0,0,0.05) 0%, rgba(0,0,0,0.2) 100%),
      url('${url}')
    `
    };
});
</script>

<template>
    <div class="service-card-wrapper h-100">
        <Link 
            :href="link" 
            class="card service-card text-decoration-none h-100" 
            :style="cardStyle" 
            @mouseover="isHovered = true" 
            @mouseleave="isHovered = false"
            :class="{ 'card-hovered': isHovered }"
        >

            <h5 class="card-title-custom">{{ title }}</h5>

            <p class="card-description-custom">{{ description }}</p>

            <div class="button-content">
                <span class="readmore">Selengkapnya</span>
                <i class="bi bi-arrow-up-right card-icon-custom"></i>
            </div>

        </Link>
    </div>
</template>

<style scoped>
/* --- STYLE ASLI ANDA (COPY PASTE) --- */

/* Wrapper */
/* Saya ubah max-width jadi width 100% agar mengikuti kolom bootstrap */
.service-card-wrapper {
    width: 100%; 
}

.service-card {
    /* Ukuran & Tampilan */
    min-height: 400px;
    border-radius: 1rem;
    border: none;
    overflow: hidden;

    /* Tambahan: Warna cadangan jika gambar gagal load */
    background-color: #333; 

    /* Background */
    background-size: cover;
    background-position: center bottom;

    /* Positioning */
    position: relative;
    /* Penting untuk pseudo-element ::before */
    color: white;

    /* Transisi untuk properti card secara umum */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    cursor: pointer;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

/* Pseudo-element untuk overlay warna */
.service-card::before {
    content: '';
    /* Wajib ada */
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: var(--hover-color);
    /* Akan diisi dari CSS custom property */
    opacity: 0;
    /* Awalnya transparan penuh */
    transition: opacity 0.5s ease;
    /* Transisi saat opacity berubah */
    z-index: 1;
    /* Di atas background, di bawah teks */
}

/* Saat di-hover, tampilkan overlay */
.service-card.card-hovered::before {
    opacity: var(--hover-opacity);
    /* Menggunakan opacity dari prop */
}

/* Custom properties untuk hover-color dan hover-opacity */
.service-card.card-hovered {
    --hover-color: v-bind('props.hoverColor');
    --hover-opacity: v-bind('props.hoverOpacity');
}


/* Judul */
.card-title-custom {
    position: absolute;
    top: 1.5rem;
    left: 1.5rem;
    font-weight: 700;
    font-size: 20px;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
    z-index: 2;
    /* Di atas overlay */
}

/* Deskripsi */
.card-description-custom {
    position: absolute;
    bottom: 4.5rem;
    left: 1.5rem;
    right: 1.5rem;
    font-size: 16px;
    line-height: 1.5;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    z-index: 2;
    /* Di atas overlay */

    opacity: 0;
    /* Default tidak terlihat */
    transform: translateY(10px);
    /* Mulai sedikit ke bawah */
    transition: opacity 0.4s ease-out, transform 0.4s ease-out;
}

/* Saat card di-hover, tampilkan deskripsi */
.service-card.card-hovered .card-description-custom {
    opacity: 1;
    transform: translateY(0);
    /* Kembali ke posisi normal */
    transition-delay: 0.1s;
}

/* Ikon */
.card-icon-custom {
    position: absolute;
    bottom: 1.5rem;
    right: 1.5rem;
    font-size: 20px;
    font-weight: bold;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
    z-index: 2;
    /* Di atas overlay */
}

.service-card.card-hovered .readmore {
    transform: translateY(0);
    opacity: 1; /* Tambahan agar muncul */
}

.readmore {
    position: absolute;
    bottom: 1.5rem;
    left: 1.5rem;
    font-size: 16px;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
    z-index: 2;

    transform: translateY(50px);
    opacity: 0; /* Tambahan default hide */

    /* Mulai sedikit ke bawah */
    transition: opacity 0.4s ease-out, transform 0.4s ease-out;
}
</style>