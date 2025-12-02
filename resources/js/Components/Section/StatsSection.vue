<script setup>
import { computed } from 'vue';
import NumberCounter from './NumberCounter.vue';

// Terima props: stats (list angka), pageSetting (judul & bg)
const props = defineProps({
    stats: Array,
    pageSetting: {
        type: Object,
        default: () => ({})
    }
});

// Logic Dynamic Background
const bgStyle = computed(() => {
    // Ambil gambar dari DB, atau fallback ke gambar default di public
    let imgUrl = props.pageSetting?.stats_bg_image;
    
    if (imgUrl) {
        // Bersihkan path double slash jika ada
        imgUrl = imgUrl.startsWith('http') ? imgUrl : `/storage/${imgUrl.replace('storage/', '')}`;
    } else {
        // Fallback Image (Pastikan ada file ini di public/images)
        imgUrl = '/images/default-stats-bg.jpg'; 
    }

    return {
        backgroundImage: `
            linear-gradient(rgba(23, 30, 49, 0.9), rgba(23, 30, 49, 0.9)),
            url('${imgUrl}')
        `
    };
});
</script>

<template>
    <div class="jumbotron bg-herosection" :style="bgStyle">
        <div class="container">
            
            <h1 class="display-4 text-herosection">
                {{ pageSetting?.stats_title || 'Pencapaian Kami' }}
            </h1>
            
            <p class="lead desc-herosection">
                {{ pageSetting?.stats_description || 'Bukti dedikasi kami dalam membangun negeri.' }}
            </p>
            
            <div class="row">
                <div class="col-12 col-md-4" v-for="(item, index) in stats" :key="item.id">
                    <div class="title-row">
                        <div class="mt-5" style="font-size:48px;font-weight: 300;">
                            <NumberCounter :target="item.value" />+
                        </div>
                        <div class="desc-row">{{ item.label }}</div>
                        <div class="border-bottom"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-herosection {
    /* Background image di-handle oleh inline style Vue */
    background-size: cover;
    background-position: center;
    color: white;
    padding: 120px 20px;
    text-align: left;
}

/* ... style lainnya tetap sama ... */
.text-herosection { font-weight: 700; margin-bottom: 20px; font-size: 32px; }
.desc-herosection { font-size: 16px; max-width: 600px; }
.desc-row { font-size: 16px; margin-top: 10px; font-weight: 500; width: 90%; }
.border-bottom { border-bottom: 3px solid red !important; width: 90%; margin-top: 30px; }
</style>