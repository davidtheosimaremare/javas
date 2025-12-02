<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '../../Layouts/MainLayout.vue';
import HeroSlider from '../../Components/Section/HeroSlider.vue';
import ServiceList from '../../Components/Section/ServiceList.vue';
import StatsSection from '../../Components/Section/StatsSection.vue';
import ProjectSlideshow from '../../Components/Section/ProjectSlideshow.vue';
import NewsCardList from '../../Components/Section/NewsCardList.vue';

defineOptions({ layout: MainLayout });
defineProps({
    slides: Array, 
    services: Array,
    stats: Array,
    projects: Array,
    recentNews: Array,
    pageSetting: Object,
});
</script>

<template>
  <Head title="Home Page" />
  <HeroSlider :slides="slides" />
  <ServiceList :items="services" :pageSetting="pageSetting" />
  <StatsSection :stats="stats" :pageSetting="pageSetting" />
  <ProjectSlideshow :items="projects" :pageSetting="pageSetting" />
  <NewsCardList :items="recentNews" />
</template>


<style scoped>
.card-home {
  margin-top: 80px;
  margin-bottom: 80px;
  cursor: pointer
}

.title {
  font-weight: 700;
  font-size: 26px;
  color: red;
}

/* --- UPDATED CSS --- */
.desc-card {
  width: 100%; /* Default untuk Mobile (HP) */
}

.desc-card p {
  font-size: 18px;
  color: #555555;
  margin-top: 30px;
}

/* Media Query untuk Tablet/Desktop (Layar > 768px) */
@media (min-width: 768px) {
  .desc-card {
    width: 50%; /* Kembali ke 50% saat di laptop/PC */
  }
}
</style>