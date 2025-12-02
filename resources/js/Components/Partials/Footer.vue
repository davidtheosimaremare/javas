<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

// 1. Ambil Data dari Middleware HandleInertiaRequests
const page = usePage();

// 2. Akses ke 'globalData' sesuai struktur Middleware Anda
const profile = computed(() => page.props.globalData?.profile || {});
const rawLinks = computed(() => page.props.globalData?.footerLinks || {});

// 3. Helper: Resolve Logo (Cek apakah dari Storage atau Public Images)
const logoSrc = computed(() => {
    const dbLogo = profile.value.logo_secondary;
    if (dbLogo) {
        // Jika path mengandung 'images/' atau 'http', itu file statis/link luar
        // Jika tidak, tambahkan '/storage/'
        return dbLogo.includes('images/') || dbLogo.startsWith('http') ? dbLogo : `/storage/${dbLogo}`;
    }
    return '/images/logo-light.png'; // Default jika DB kosong
});

// 4. Logic Social Media (Object DB -> Array Icon)
const socialList = computed(() => {
    const social = profile.value.social_media || {}; 
    const list = [];

    // Mapping manual agar urutan & icon sesuai
    if (social.facebook) list.push({ name: 'Facebook', url: social.facebook, icon: 'bi-facebook' });
    if (social.instagram) list.push({ name: 'Instagram', url: social.instagram, icon: 'bi-instagram' });
    if (social.linkedin) list.push({ name: 'LinkedIn', url: social.linkedin, icon: 'bi-linkedin' });
    if (social.twitter) list.push({ name: 'Twitter', url: social.twitter, icon: 'bi-twitter-x' });
    if (social.youtube) list.push({ name: 'YouTube', url: social.youtube, icon: 'bi-youtube' });
    if (social.tiktok) list.push({ name: 'TikTok', url: social.tiktok, icon: 'bi-tiktok' });

    return list;
});

// 5. Grouping Links (Asumsi di DB kolom type isinya: 'internal', 'partner', 'service')
// Sesuaikan string di dalam [] dengan isi kolom 'type' di database Anda
const internalLinks = computed(() => rawLinks.value['internal'] || []);
const serviceLinks = computed(() => rawLinks.value['service'] || []);
const partnerLinks = computed(() => rawLinks.value['partner'] || []);

const currentYear = new Date().getFullYear();
</script>

<template>
  <footer class="footer-section">
    <div class="container pt-5 pb-4">
      <div class="row">

        <div class="col-lg-5 col-md-6 mb-4">
          <div class="footer-logo mb-3">
            <img :src="logoSrc" :alt="profile.company_name || 'JBB Group'" width="120">
          </div>

          <div class="contact-info text-white fs-7">
            <p class="mb-4" style="line-height: 1.6;">
                {{ profile.company_description ? profile.company_description.substring(0, 160) + '' : 'Solusi infrastruktur kelistrikan terpercaya.' }}
            </p>
            
            <div class="d-flex mb-3">
                <i class="bi bi-geo-alt-fill text-warning me-3 mt-1 flex-shrink-0"></i>
                <span class="text-white" style="white-space: pre-line;">
                    {{ profile.company_address || 'Alamat belum diatur.' }}
                </span>
            </div>
            
            <div class="d-flex mb-2">
                <i class="bi bi-telephone-fill text-warning me-3 flex-shrink-0"></i>
                <span class="text-white">{{ profile.phone_number || '-' }}</span>
            </div>

            <div class="d-flex mb-3">
                <i class="bi bi-envelope-fill text-warning me-3 flex-shrink-0"></i>
                <span class="text-white">{{ profile.company_email || '-' }}</span>
            </div>
          </div>

          <div class="social-icons d-flex gap-2 mt-4" v-if="socialList.length > 0">
            <a v-for="(item, index) in socialList" :key="index" :href="item.url" target="_blank" class="social-btn" :title="item.name">
                <i :class="['bi', item.icon]"></i>
            </a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 col-6 mb-4">
          <h6 class="footer-heading">Navigasi</h6>
          
          <ul class="footer-links list-unstyled" v-if="internalLinks.length > 0">
            <li v-for="link in internalLinks" :key="link.id">
              <Link :href="link.url">{{ link.title }}</Link> 
            </li>
          </ul>

          <ul class="footer-links list-unstyled" v-else>
            <li><Link href="/">Home</Link></li>
            <li><Link href="/about">About Us</Link></li>
            <li><Link href="/projects">Projects</Link></li>
            <li><Link href="/news">News</Link></li>
            <li><Link href="/career">Career</Link></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 col-6 mb-4">
          <h6 class="footer-heading">Layanan</h6>
          
          <ul class="footer-links list-unstyled" v-if="serviceLinks.length > 0">
            <li v-for="link in serviceLinks" :key="link.id">
                <a :href="link.url">{{ link.title }}</a>
            </li>
          </ul>

          <ul class="footer-links list-unstyled" v-else>
            <li><a href="#">EPC Power</a></li>
            <li><a href="#">Maintenance</a></li>
            <li><a href="#">Panel Maker</a></li>
            <li><a href="#">General Supplier</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6 mb-4 text-md-end text-start">
          <div class="wbs-button-wrapper d-flex justify-content-md-end">
            <Link href="/contact" class="wbs-btn">
              <i class="bi bi-whatsapp me-2 fs-5"></i>
              <div class="text-start lh-1">
                <span class="d-block fw-bold">Hubungi Kami</span>
                <small style="font-size: 11px;">Konsultasi Gratis</small>
              </div>
            </Link>
          </div>
        </div>
      </div>

      <hr class="footer-divider my-4">

      <div class="row align-items-center">
        <div class="col-md-6 text-center text-md-start">
          <p class="copyright-text mb-0">
            &copy; {{ currentYear }} <strong>{{ profile.company_name || 'PT JBB Javas Berkah Bistari' }}</strong>. All Rights Reserved.
          </p>
        </div>
        <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
             <a href="#" class="text-decoration-none text-white-50 ms-3 small">Syarat & Ketentuan</a>
             <a href="#" class="text-decoration-none text-white-50 ms-3 small">Kebijakan Privasi</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.footer-section {
  background-color: #002b49;
  color: #fff;
  margin-top: 0;
  position: relative;
  overflow: hidden;
}

.fs-7 { font-size: 0.95rem; }
.footer-heading { 
    font-weight: 700; margin-bottom: 1.5rem; 
    text-transform: uppercase; font-size: 0.9rem; 
    letter-spacing: 1px; color: #fff;
    border-bottom: 2px solid rgba(255,255,255,0.1);
    padding-bottom: 10px;
    display: inline-block;
}

.footer-links li { margin-bottom: 12px; }
.footer-links a { 
    color: #cfd8dc; text-decoration: none; font-size: 0.9rem; 
    transition: all 0.3s; display: block;
}
.footer-links a:hover { color: #fff; transform: translateX(5px); }

/* Social Icons Style */
.social-btn { 
    color: #fff; font-size: 1.1rem; transition: all 0.3s; 
    width: 40px; height: 40px; background: rgba(255,255,255,0.1); 
    display: flex; align-items: center; justify-content: center; 
    border-radius: 50%; text-decoration: none; border: 1px solid transparent;
}
.social-btn:hover { 
    transform: translateY(-3px); 
    background: #e63946; 
    border-color: #e63946;
    box-shadow: 0 5px 15px rgba(230, 57, 70, 0.4);
}

/* CTA Button */
.wbs-btn {
  display: inline-flex; align-items: center; 
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2); 
  padding: 10px 25px; border-radius: 50px;
  color: #fff; text-decoration: none; transition: all 0.3s;
}
.wbs-btn:hover { 
    background: #25d366; border-color: #25d366; 
    box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
}

.footer-divider { border-color: rgba(255, 255, 255, 0.1); }
.copyright-text { font-size: 0.85rem; color: #adb5bd; }

@media (max-width: 768px) {
  .wbs-button-wrapper { justify-content: center !important; margin-top: 20px; }
  .footer-logo { text-align: center; }
  .social-icons { justify-content: center; }
  .contact-info { text-align: center; }
  .contact-info .d-flex { justify-content: center; }
}
</style>