<script setup>
import ServiceCard from './ServiceCard.vue';

defineProps({
    items: {
        type: Array,
        required: true
    },
    pageSetting: {
        type: Object,
        default: () => ({}) // Default object kosong agar tidak error jika null
    }
});
</script>

<template>
    <div class="container card-home px-4">
    <div class="title-card mb-20">
        <h2 class="title ">{{ pageSetting?.service_title || 'Jelajahi Ekosistems JBB' }}</h2>
      </div>
      <div class="desc-card mb-5">
        <p>{{ pageSetting?.service_description || 'Kami adalah mitra strategis Anda dalam EPC Power. Kami merancang, membangun, dan memelihara infrastruktur kelistrikan yang presisi, andal, dan berkelanjutan.' }}</p>
      </div>
      
      <div class="row g-4 justify-content-center">
        <div 
            class="col-12 col-md-6 col-lg-3 d-flex align-items-stretch" 
            v-for="(item, index) in items" 
            :key="index"
        >
            <ServiceCard 
                :title="item.title" 
                :image-url="item.image" 
                :description="item.description"
                :hover-color="item.color"
                :link="item.link"
            />
        </div>
       </div>
    </div>
</template>