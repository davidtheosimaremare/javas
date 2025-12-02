<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3'; 
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Autoplay, Navigation } from 'swiper/modules';

// import 'swiper/css';
// import 'swiper/css/navigation';


defineProps({
  slides: {
    type: Array,
    required: true
  }
});

// --- STATE ---
const swiperInstance = ref(null);
const activeIndex = ref(0);
const autoplayProgress = ref(0);

// --- METHODS ---
const onSwiper = (swiper) => {
  swiperInstance.value = swiper;
};

const onSlideChange = (swiper) => {
  activeIndex.value = swiper.realIndex;
};

const goToSlide = (index) => {
  swiperInstance.value.slideToLoop(index);
};

// Fungsi Progress Bar
const onAutoplayTimeLeft = (swiper, time, progress) => {
  autoplayProgress.value = 1 - progress;
};
</script>

<template>
  <div class="hero-slider">

    <swiper 
      :modules="[Autoplay, Navigation]" 
      :slides-per-view="1" 
      :loop="true" 
      :autoplay="{ delay: 5000, disableOnInteraction: false }" 
      @swiper="onSwiper" 
      @slideChange="onSlideChange" 
      @autoplayTimeLeft="onAutoplayTimeLeft"
    >
      <swiper-slide v-for="slide in slides" :key="slide.id">
        <div class="slide-content" :style="{ backgroundImage: `url(${slide.image})` }">
          <div class="container px-4 px-lg-4">
            
            <h1 class="mainTitle">{{ slide.title }}</h1>
            <span class="slide-desc">{{ slide.desc }}</span>
            
            <div class="button-slider">
              <Link :href="slide.link || '#'" class="btn btn-slider btn-outline-danger btn-lg rounded-pill px-3 fw-bold text-white border-light hover-red">
                Selengkapnya <i class="bi bi-arrow-up-right"></i>
              </Link>
            </div>

          </div>
        </div>
      </swiper-slide>
    </swiper>

    <div class="slider-nav container px-4 px-lg-4">
      <div 
        v-for="(slide, index) in slides" 
        :key="slide.id" 
        class="nav-item" 
        :class="{ active: index === activeIndex }"
        @click="goToSlide(index)"
      >
        <div class="progress-bar" :style="{ width: index === activeIndex ? (autoplayProgress * 100) + '%' : '0%' }">
        </div>
        
        <span class="nav-title">{{ slide.navTitle }}</span>
        
        <span class="border"></span>
        
        <div class="nav-popup">
          <span class="popup-title">{{ slide.navTitle }}</span>
          <img :src="slide.image" alt="thumbnail" class="popup-image" />
        </div>
      </div>
    </div>

  </div>
</template>

<style scoped>
/* =======================================
   STYLE VISUAL (BASE)
   ======================================= */
.hero-slider {
  position: relative;
  width: 100%;
  margin-top: -85px; 
  height: 750px;
  background: #1a1a1a;
}

.swiper {
  width: 100%;
  height: 100%;
}

.slide-content {
  position: relative;
  z-index: 1;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
  
}

.slide-content::before {
  content: "";
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: linear-gradient(to right, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.5) 50%, transparent 100%);
  z-index: 1;
}

.slide-content :deep(.container) {
  position: relative;
  z-index: 2;
}

.mainTitle {
  color: white;
  font-size: 42px;
  font-weight: 500;
 
  max-width: 50%;
  margin-top: -50px;
  margin-bottom: 10px;
}

.slide-desc {
  display: block;
  margin-top: 15px;
  color: white;
  font-size: 18px;
  max-width: 50%;
  text-shadow: 1px 1px 6px rgba(0, 0, 0, 0.6);
}

.button-slider {
  margin-top: 20px;
  
}

.btn-slider{
    font-size:16px
}
.btn-slider:hover{
    border:none;
}

/* =======================================
   NAVIGASI BAWAH 
   ======================================= */
.slider-nav {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  display: flex;
  z-index: 10;
  gap: 50px;
  padding-bottom: 0;
 
  margin-bottom: 50px;
}

.nav-item {
  flex: 1;
  position: relative;
  cursor: pointer;
  padding: 30px 0; /* Area sentuh luas */
  color: rgba(255, 255, 255, 0.6); 
  font-weight: normal;
  font-size: 14px;
  display: flex;
  justify-content: space-between;
  transition: color 0.3s ease;
}

/* Saat Hover/Active: Teks jadi Putih Terang */
.nav-item:hover, .nav-item.active {
  color: #ffffff;
}

/* Garis Dasar (Putih Transparan) */
.border {
  position: absolute;
  bottom: 0; left: 0;
  height: 1px;
  width: 100%;
  background-color: rgba(255, 255, 255, 0.3);
  z-index: 1;
}

/* Progress Bar Merah */
.progress-bar {
  position: absolute;
  bottom: 0; left: 0;
  height: 3px;
  background-color: #dc3545; /* Merah Pertamina */
  width: 0%;
  z-index: 20; /* Di atas segalanya */
  transition: width linear;
}

/* Sembunyikan garis merah saat hover */
.nav-item:hover .progress-bar {
  opacity: 0;
}

/* JUDUL NAVIGASI */
.nav-title {
  position: absolute;
  top: 60%; left: 0;
  transform: translateY(-50%);
  text-align: left;
  font-weight: 500;
  font-size: 14px;
  letter-spacing: 1px;
  text-transform: uppercase;
  opacity: 1;
 transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 2;
}

.nav-item:hover .nav-title {
  opacity: 0; /* Hilang total */
  transform: translateY(-50px); /* Naik sedikit */
  font-size:14px;
}

.nav-popup {
  position: absolute;
  bottom: 0; 
  left: 0; right: 0;
  border-radius: 6px 6px 0 0;
  z-index: 10; 
  height: 0; 
  overflow: hidden; 
  opacity: 0; 
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

}

/* SAAT HOVER: TUMBUH KE ATAS */
.nav-item:hover .nav-popup {
  height: 200px; /* Target Tinggi (Setara h-[150px] tapi disesuaikan layout kita) */
  opacity: 1;
}

.popup-title {
  display: block;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 5px;
  padding-left: 2px;
   letter-spacing: 1px;
  color: #fff;
}

/* Agar gambar tetap proporsional saat container berubah ukuran */
.popup-image {
  width: 100%;
  height: 200px; /* Tinggi fix gambar */
  object-fit: cover;
  border-radius: 6px 6px 0 0;
  display: block;
}

@media (max-width: 768px) {
  .hero-slider { height: 100vh; margin-top: 0; }
  .mainTitle { font-size: 2rem; margin-top: 0; max-width: 100%; }
  .slide-desc { font-size: 1rem; max-width: 100%; }
  
  /* 1. CONTAINER NAVIGASI MOBILE */
  .slider-nav { 
    display: flex; 
    width: 100%;
    gap: 8px; /* Jarak antar item */
    bottom: 20px; 
    margin-bottom: 0;
    padding: 0 15px; /* Padding kiri kanan layar */
  }

  /* 2. ITEM NAVIGASI (FLEKSIBEL) */
  .nav-item {
    position: relative;
    padding: 0 0 10px 0; /* Ruang untuk teks di atas garis */
    text-align: center;
    border: none;
    
    /* LOGIKA PANJANG PENDEK: */
    flex: 1; /* Default: Pendek (berbagi ruang sisa) */
    
    /* Track Garis (Abu-abu) */
    border-bottom: 2px solid rgba(255, 255, 255, 0.3);
    
    /* Animasi saat memanjang/memendek */
    transition: flex 0.4s ease-in-out; 
  }

  /* SAAT AKTIF: MENJADI LEBIH PANJANG */
  .nav-item.active {
    flex: 2.5; /* Mengambil ruang 2.5x lipat dari yang lain */
    opacity: 1;
  }
  
  /* 3. JUDUL NAVIGASI KECIL (STATIS) */
  .nav-title {
    position: static;
    transform: none;
    display: block !important;
    
    font-size: 10px;
    margin-bottom: 5px;
    text-align: center;
    width: 100%;
    
    /* Agar teks tidak putus */
    white-space: nowrap; 
    overflow: hidden;
    text-overflow: ellipsis;
    
    /* Pastikan warna terlihat */
    opacity: 0.7; 
  }
  
  .nav-item.active .nav-title {
    opacity: 1;
    font-weight: bold;
  }

  /* 4. PROGRESS BAR (ISI MERAH) */
  .progress-bar {
    bottom: -2px; 
    height: 2px;
    opacity: 1 !important; 
    /* Lebar progress bar dihandle oleh Vue via style binding */
  }
  
  /* Sembunyikan elemen Desktop Popup di Mobile */
  .border, 
  .nav-popup {
    display: none !important;
  }

  /* Reset efek hover desktop */
  .nav-item:hover .nav-title {
    opacity: 1;
    transform: none;
  }
}

</style>