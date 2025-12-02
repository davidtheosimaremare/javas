<script setup>
import { Link, router } from '@inertiajs/vue3' 
import { ref, onMounted, onUnmounted, nextTick } from 'vue'

// --- ASSETS ---
const logoLight = '/images/logo-light.png'
const logoColor = '/images/logo.png'
// Bendera
const indoFlag = '/images/indo-flag.png'
const engFlag = '/images/en-flag.png' // <-- BARU: Tambahkan ini (pastikan file ada di folder public/images)

const currentLang = ref('ID')

// --- MENU DATA ---
const MenuItems = ref([
  { name: 'Home', path: '/' },
  { name: 'About Us', path: '/about' },
  { name: 'Project Reference', path: '/projects' },
  { name: 'Career', path: '/career' },
  { name: 'News', path: '/news' },
  { name: 'Contact', path: '/contact' },
])

// --- SCROLL LOGIC ---
const isScrolled = ref(false)
const handleScroll = () => { isScrolled.value = window.scrollY > 0 }

// --- SEARCH LOGIC ---
const isSearchOpen = ref(false)
const searchQuery = ref('')
const searchInputRef = ref(null)

const openSearch = () => {
  isSearchOpen.value = true
  document.body.style.overflow = 'hidden'
  nextTick(() => { searchInputRef.value?.focus() })
}

const closeSearch = () => {
  isSearchOpen.value = false
  document.body.style.overflow = 'auto'
  searchQuery.value = ''
}

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    closeSearch()
    router.get('/news', { q: searchQuery.value }) 
  }
}

// --- CONTACT MODAL ---
const isContactModalOpen = ref(false)
const openContactModal = () => { isContactModalOpen.value = true }
const closeContactModal = () => { isContactModalOpen.value = false }

// --- GOOGLE TRANSLATE LOGIC ---

// 1. Helper Cookies
const setCookie = (key, value, expiry) => {
  const expires = new Date()
  expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 1000))
  document.cookie = key + '=' + value + ';expires=' + expires.toUTCString() + ';path=/'
}

const getCookie = (key) => {
  const keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)')
  return keyValue ? keyValue[2] : null
}

const deleteCookie = (name) => {
  document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;'
  document.cookie = name + '=; Path=/; Domain=' + document.domain + '; Expires=Thu, 01 Jan 1970 00:00:01 GMT;'
}

// 2. Fungsi Ganti Bahasa
const changeLanguage = (lang) => {
  if (lang === 'EN') {
    // Set cookie Google Translate: /auto/en (Dari Auto/Indo ke English)
    setCookie('googtrans', '/auto/en', 1)
    setCookie('googtrans', '/auto/en', 1) // Set ulang untuk memastikan domain
  } else {
    // Hapus cookie untuk kembali ke default (ID)
    deleteCookie('googtrans')
  }
  // Reload halaman untuk menerapkan/menghapus terjemahan
  window.location.reload()
}

// 3. Load Script Google Translate
const loadGoogleTranslate = () => {
  if (!document.getElementById('google-translate-script')) {
    const script = document.createElement('script')
    script.id = 'google-translate-script'
    script.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit'
    document.body.appendChild(script)
    
    window.googleTranslateElementInit = () => {
      new window.google.translate.TranslateElement(
        { pageLanguage: 'id', includedLanguages: 'en,id', autoDisplay: false },
        'google_translate_element'
      )
    }
  }
}

// --- LIFECYCLE ---
onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      if (isSearchOpen.value) closeSearch()
      if (isContactModalOpen.value) closeContactModal()
    }
  })

  // Cek Cookie saat load untuk set state tombol aktif
  const cookie = getCookie('googtrans')
  if (cookie && cookie.includes('/en')) {
    currentLang.value = 'EN'
  } else {
    currentLang.value = 'ID'
  }

  // Load Google Translate Script
  loadGoogleTranslate()
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
  <div>
    <div id="google_translate_element" style="display:none;"></div>

    <div class="top-bar-custom d-none d-lg-block">
      <div class="container px-4 px-lg-4">
        <button type="button" 
            class="btn btn-lang" 
            :class="{ 'active': currentLang === 'ID' }"
            @click="changeLanguage('ID')">
            ID <span class="flag-icon"><img :src="indoFlag" alt="ID"></span>
        </button>
        
        <button type="button" 
            class="btn btn-lang text-white" 
            :class="{ 'active': currentLang === 'EN' }"
            @click="changeLanguage('EN')">
            EN <span class="flag-icon"><img :src="engFlag" alt="EN"></span>
        </button>

        <span class="top-bar-contact ms-3" @click="openContactModal">Hubungi Kami</span>
      </div>
    </div>

    <nav class="navbar navbar-expand-lg fixed-top" :class="{ 'scrolled': isScrolled }">
      <div class="container px-4 px-lg-4">

        <Link class="navbar-brand d-lg-none" href="/">
            <img :src="isScrolled ? logoColor : logoLight" width="40" alt="Logo">
        </Link>

        <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu"
          aria-controls="mobileMenu">
          <span class="navbar-toggler-icon" :style="isScrolled ? '' : 'filter: invert(1) grayscale(100%) brightness(200%);'"></span>
        </button>

        <div class="collapse navbar-collapse d-none d-lg-block" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="icon-search" @click="openSearch">
              <i class="bi bi-search" :class="isScrolled ? 'text-dark' : 'text-white'"></i>
            </li>
            <li v-for="(item, index) in MenuItems" :key="index" class="nav-item">
              <Link class="nav-link b" :class="isScrolled ? 'text-dark' : 'text-white'" :href="item.path">
                {{ item.name }}
              </Link>
            </li>
          </ul>
          <Link class="navbar-brand ms-auto" href="/">
            <img :src="isScrolled ? logoColor : logoLight" width="50" height="50" alt="Logo">
          </Link>
        </div>

      </div>
    </nav>

    <div class="offcanvas offcanvas-end w-100 bg-white" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
      
      <div class="offcanvas-header pt-3 pb-2 px-4 d-flex align-items-center justify-content-between">
         <i class="bi bi-search fs-4 text-dark" @click="openSearch"></i>
         <div class="d-flex align-items-center">
            <div class="mobile-lang me-3">
               <span 
                 class="lang-opt" 
                 :class="{ 'active': currentLang === 'ID' }"
                 @click="changeLanguage('ID')">ID</span>
               
               <img :src="indoFlag" width="16" class="mx-2" alt="ID">
               
               <span 
                 class="lang-opt" 
                 :class="{ 'active': currentLang === 'EN' }"
                 @click="changeLanguage('EN')">EN</span>
                <img :src="engFlag" width="16" class="ms-2" alt="EN">
            </div>

            <button type="button" class="btn-close-custom" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="bi bi-x-lg"></i>
            </button>
         </div>
      </div>

      <div class="offcanvas-body pt-4 px-4 d-flex flex-column">
        
        <ul class="navbar-nav mobile-nav-list mb-4">
          <li v-for="(item, index) in MenuItems" :key="index" class="mobile-nav-item">
            <Link class="mobile-nav-link" :href="item.path">
                {{ item.name }}
                <i class="bi bi-chevron-right text-muted fs-6"></i>
            </Link>
          </li>
        </ul>

        <div class="mt-auto mb-4">
             <button class="btn btn-outline-danger w-100 rounded-pill fw-bold py-2" @click="openContactModal">
                HUBUNGI KAMI
             </button>
        </div>

      </div>
    </div>

    <Transition name="fade">
      <div v-if="isSearchOpen" class="search-overlay">
        <button class="close-btn" @click="closeSearch"><i class="bi bi-x-lg"></i></button>
        <div class="container h-100 d-flex flex-column justify-content-center align-items-center">
          <div class="search-content text-center w-100">
            <h3 class="text-white mb-4">Apa yang ingin Anda cari?</h3>
            <div class="input-group input-group-lg">
              <input ref="searchInputRef" v-model="searchQuery" type="text" class="form-control search-input"
                placeholder="Ketik kata kunci..." @keyup.enter="handleSearch">
            </div>
            <p class="text-white-50 mt-3 small">Tekan ESC untuk menutup</p>
          </div>
        </div>
      </div>
    </Transition>

    <Transition name="fade">
      <div v-if="isContactModalOpen" class="contact-overlay" @click.self="closeContactModal">
        <div class="contact-modal-card">
          <button class="modal-close-icon" @click="closeContactModal">&times;</button>
          <h4 class="text-blue fw-bold mb-4 text-center">Pilih Metode Kontak</h4>
          <div class="d-grid gap-3">
            <a href="https://wa.me/6281249009899" target="_blank" class="btn btn-whatsapp py-3">
              <i class="bi bi-whatsapp me-2 fs-5"></i>
              <div><span class="d-block fw-bold">Chat WhatsApp</span><small>+62 812-4900-9899</small></div>
            </a>
            <a href="mailto:info@jbb.co.id" class="btn btn-email py-3">
              <i class="bi bi-envelope-at-fill me-2 fs-5"></i>
              <div><span class="d-block fw-bold">Kirim Email</span><small>info@jbb.co.id</small></div>
            </a>
          </div>
          <div class="text-center mt-4"><small class="text-muted">Senin - Jumat | 08.00 - 17.00 WIB</small></div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<style scoped>
/* --- STYLE DESKTOP --- */
.nav-item { padding-right: 20px; }
.nav-link { font-weight: bold; font-size: 16px; text-transform: uppercase; transition: color 0.3s; }
.navbar { font-weight: bold; z-index: 1000; background: transparent; transition: all 0.3s ease-in-out; margin-top: 50px; }

/* Styling Tombol Bahasa Desktop */
.btn-lang {
    background: transparent;
    border: none;
    color: #ffffffaa; /* Agak transparan kalau tidak aktif */
    font-weight: 600;
    font-size: 15px;
    padding: 3px 10px;
    border-radius: 50px;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
}
.btn-lang:hover { color: #fff; }
.btn-lang.active {
    background-color: transparent;
    color: #fff;
    border: 1px solid #fff; /* Border hanya untuk yang aktif */
    opacity: 1;
}

.flag-icon img { width: 20px; margin-left: 5px; }

.navbar-toggler-icon{ color:white !important; }
.navbar-toggler{ color:white !important; }
.navbar-toggler:focus, .navbar-toggler:active { outline: none !important; box-shadow: none !important; border-color: transparent !important; }

@media (max-width: 991.98px) { .navbar { margin-top: 0; padding: 15px 0; } }
.navbar.scrolled { background-color: #fff; padding: 0 0; height: 70px; opacity: 1; margin-top: 0px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
.navbar.scrolled .container { height: 100%; display: flex; align-items: center; }
.navbar-brand { color: #fff; font-weight: bold; font-size: 24px; text-transform: uppercase; }
.icon-search { margin-top: 7px; padding-right: 20px; color: #fff; cursor: pointer; transition: transform 0.2s; }
.icon-search:hover { transform: scale(1.1); }
.top-bar-custom { position: relative; background-color: transparent; color: #fff; line-height: 30px; z-index: 1001; margin-top: 20px; }
.top-bar-contact { font-size: 15px; cursor: pointer; font-weight: 600; transition: color 0.3s; }
.top-bar-contact:hover { color: #e63946; text-decoration: none; }

/* --- STYLE MOBILE SIDEBAR --- */
.offcanvas { background-color: #fff !important; }

/* Lang Toggle Mobile */
.mobile-lang {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 50px;
    padding: 6px 15px;
    display: flex; align-items: center;
    font-size: 13px; font-weight: bold;
}
.mobile-lang .lang-opt {
    cursor: pointer;
    color: #aaa;
    transition: color 0.3s;
}
.mobile-lang .lang-opt.active {
    color: #000;
}

.btn-close-custom {
    background-color: #dc3545;
    color: white; border: none; width: 40px; height: 40px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; border-radius: 0; cursor: pointer;
}

.mobile-nav-list { display: flex; flex-direction: column; }
.mobile-nav-item { border-bottom: 1px solid #f0f0f0; }
.mobile-nav-link {
    display: flex; justify-content: space-between; align-items: center;
    padding: 18px 0; color: #212529; font-weight: 700; 
    font-size: 14px; text-transform: uppercase; text-decoration: none; letter-spacing: 0.5px;
}
.mobile-nav-link:hover { color: #dc3545; }

/* --- SEARCH & CONTACT MODAL --- */
.search-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 43, 73, 0.95); z-index: 9999; backdrop-filter: blur(5px); }
.search-content { max-width: 700px; margin: 0 auto; }
.search-input { background: transparent; border: none; border-bottom: 2px solid rgba(255, 255, 255, 0.5); border-radius: 0; color: #fff; font-size: 1.5rem; padding: 15px 0; text-align: center; }
.search-input:focus { background: transparent; box-shadow: none; border-bottom-color: #fff; color: #fff; }
.search-input::placeholder { color: rgba(255, 255, 255, 0.3); font-style: italic; }
.close-btn { position: absolute; top: 30px; right: 40px; background: none; border: none; color: #fff; font-size: 3rem; cursor: pointer; transition: transform 0.3s; }
.close-btn:hover { transform: rotate(90deg); color: #e63946; }
.contact-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.6); z-index: 9999; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(3px); }
.contact-modal-card { background: #fff; width: 90%; max-width: 400px; border-radius: 15px; padding: 30px; position: relative; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2); animation: popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.modal-close-icon { position: absolute; top: 10px; right: 15px; background: none; border: none; font-size: 2rem; color: #999; cursor: pointer; }
.modal-close-icon:hover { color: #333; }
.btn-whatsapp { background-color: #25d366; color: #fff; border: none; display: flex; align-items: center; justify-content: center; border-radius: 10px; transition: transform 0.2s; }
.btn-whatsapp:hover { background-color: #1ebc57; color: #fff; transform: translateY(-3px); }
.btn-email { background-color: #002b49; color: #fff; border: none; display: flex; align-items: center; justify-content: center; border-radius: 10px; transition: transform 0.2s; }
.btn-email:hover { background-color: #001f36; color: #fff; transform: translateY(-3px); }
.text-blue { color: #002b49; }
@keyframes popIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>

<style>
/* Sembunyikan banner Google Translate di atas */
.goog-te-banner-frame.skiptranslate {
    display: none !important;
} 
body {
    top: 0px !important; 
}
/* Sembunyikan elemen widget asli */
#google_translate_element {
    display: none !important;
}
/* Sembunyikan tooltip Google saat hover text */
.goog-tooltip {
    display: none !important;
}
.goog-tooltip:hover {
    display: none !important;
}
.goog-text-highlight {
    background-color: transparent !important;
    box-shadow: none !important;
}

.skiptranslate{
  visibility: hidden !important;
}
</style>