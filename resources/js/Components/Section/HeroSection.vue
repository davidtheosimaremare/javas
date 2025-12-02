<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: String,
    subtitle: String,
    primaryBtnText: String,
    primaryBtnLink: {
        type: String,
        default: '#'
    },
    bgImage: { // Opsional: jika ingin background image
        type: String,
        default: null
    }
});
</script>

<template>
    <div 
        class="p-5 mb-4 bg-light rounded-3 border shadow-sm position-relative overflow-hidden"
        :style="bgImage ? `background: url(${bgImage}) center/cover no-repeat;` : ''"
    >
        <!-- Overlay jika ada gambar agar teks terbaca -->
        <div v-if="bgImage" class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50"></div>

        <div class="container-fluid py-5 position-relative text-center">
            <h1 class="display-5 fw-bold" :class="bgImage ? 'text-white' : 'text-dark'">
                {{ title }}
            </h1>
            <p class="fs-4 col-md-8 mx-auto" :class="bgImage ? 'text-white' : ''">
                {{ subtitle }}
            </p>
            <div class="mt-4" v-if="primaryBtnText">
                <Link :href="primaryBtnLink" class="btn btn-primary btn-lg px-4 me-2">
                    {{ primaryBtnText }}
                </Link>
            </div>
        </div>
    </div>
</template>