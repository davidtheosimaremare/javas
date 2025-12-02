<script setup>
// Menerima data slides berupa Array
// Contoh format: [{ image: 'url', title: 'Judul', desc: 'Deskripsi' }]
defineProps({
    slides: Array,
    id: {
        type: String,
        default: 'mainCarousel' // Default ID agar tidak bentrok jika ada 2 slider
    }
});
</script>

<template>
    <div :id="id" class="carousel slide mb-5" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button 
                v-for="(slide, index) in slides" 
                :key="index"
                type="button" 
                :data-bs-target="`#${id}`" 
                :data-bs-slide-to="index" 
                :class="{ active: index === 0 }"
                aria-current="true"
            ></button>
        </div>
        
        <div class="carousel-inner rounded shadow">
            <div 
                v-for="(slide, index) in slides" 
                :key="index" 
                class="carousel-item" 
                :class="{ active: index === 0 }"
            >
                <!-- Placeholder gambar -->
                <img :src="slide.image" class="d-block w-100" :alt="slide.title" style="height: 500px; object-fit: cover;">
                
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                    <h5>{{ slide.title }}</h5>
                    <p>{{ slide.desc }}</p>
                </div>
            </div>
        </div>
        
        <button class="carousel-control-prev" type="button" :data-bs-target="`#${id}`" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" :data-bs-target="`#${id}`" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</template>