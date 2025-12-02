<script setup>
import { onMounted } from 'vue'
import { Head, useForm } from '@inertiajs/vue3' // Pakai useForm
import MainLayout from '@/Layouts/MainLayout.vue'
import HeroPage from '@/Components/Section/HeroPage.vue'

// --- 1. TERIMA DATA DARI CONTROLLER ---
const props = defineProps({
    profile: Object,      // Data Company Profile
    pageSetting: Object   // Data Hero Image
})

// --- 2. FORM LOGIC (INERTIA) ---
const form = useForm({
    name: '',
    company: '',
    email: '',
    phone: '',
    service_type: '', // PERBAIKAN: Gunakan snake_case sesuai database/controller
    location: '',
    message: '',
    file: null
})

const submitForm = () => {
    // Post ke route contact.store
    form.post('/contact', {
        preserveScroll: true, // Agar tidak scroll ke atas jika ada error
        forceFormData: true,  // Penting untuk upload file
        onSuccess: () => {
            form.reset() // Bersihkan form jika sukses
        },
        onError: (errors) => {
            console.log("Gagal Submit:", errors); // Cek console jika gagal
        }
    })
}

// Handler File Upload
const handleFileUpload = (event) => {
    form.file = event.target.files[0]
}

onMounted(() => {
    window.scrollTo(0, 0)
})
</script>

<template>
    <Head title="Hubungi Kami" />

    <MainLayout>
        
        <div class="contact-page pb-5">

            <div class="hero-mobile-fix">
                <HeroPage 
                    :title="pageSetting?.title || 'Hubungi Kami'" 
                    :bgImage="pageSetting?.hero_bg || 'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=2000&auto=format&fit=crop'" 
                />
            </div>

            <div class="container py-5">
                <div class="row gx-lg-5">

                    <div class="col-lg-5 mb-5 mb-lg-0">
                        <div class="contact-info-wrapper">
                            <h2 class="section-title mb-3">Konsultasi Proyek</h2>
                            <p class="text-muted mb-4">
                                Butuh solusi kelistrikan untuk pabrik atau gedung Anda? Tim engineer 
                                <span class="fw-bold">{{ profile?.company_name || 'JBB Group' }}</span> 
                                siap memberikan solusi teknis terbaik.
                            </p>

                            <div class="info-item d-flex mb-4">
                                <div class="icon-box"><i class="bi bi-geo-alt-fill"></i></div>
                                <div class="text-box">
                                    <h5 class="fw-bold mb-1">Kantor Pusat</h5>
                                    <p class="text-muted mb-0" style="white-space: pre-line;">{{ profile?.company_address || 'Alamat belum diisi' }}</p>
                                </div>
                            </div>

                            <div class="info-item d-flex mb-4">
                                <div class="icon-box"><i class="bi bi-whatsapp"></i></div>
                                <div class="text-box">
                                    <h5 class="fw-bold mb-1">Hotline Sales</h5>
                                    <p class="text-muted mb-0">
                                        <a v-if="profile?.phone_number" :href="`https://wa.me/${profile.phone_number.replace(/[^0-9]/g, '')}`" class="text-decoration-none text-muted hover-blue">
                                            {{ profile.phone_number }}
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="info-item d-flex mb-4">
                                <div class="icon-box"><i class="bi bi-envelope-fill"></i></div>
                                <div class="text-box">
                                    <h5 class="fw-bold mb-1">Email</h5>
                                    <p class="text-muted mb-0">
                                        <a v-if="profile?.company_email" :href="`mailto:${profile.company_email}`" class="text-decoration-none text-muted hover-blue">
                                            {{ profile.company_email }}
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="map-container mt-5 rounded-4 overflow-hidden shadow-sm">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.059296541571!2d106.78119797499066!3d-6.255919693732626!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f1a239999999%3A0x1234567890abcdef!2sJakarta!5e0!3m2!1sid!2sid!4v1234567890" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="form-wrapper p-5 rounded-4 shadow-sm bg-white border">

                            <div class="mb-4">
                                <h3 class="form-title">Sampaikan Kebutuhan Anda</h3>
                                <p class="text-muted">Isi formulir untuk mendapatkan penawaran harga.</p>
                            </div>

                            <div v-if="form.recentlySuccessful" class="alert alert-success d-flex align-items-center mb-4">
                                <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                                <div>Terima kasih! Permintaan Anda berhasil terkirim.</div>
                            </div>

                            <form @submit.prevent="submitForm">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Nama Lengkap</label>
                                        <input v-model="form.name" type="text" class="form-control" :class="{'is-invalid': form.errors.name}" placeholder="Bpk/Ibu..." required>
                                        <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Nama Perusahaan</label>
                                        <input v-model="form.company" type="text" class="form-control" :class="{'is-invalid': form.errors.company}" placeholder="PT..." required>
                                        <div v-if="form.errors.company" class="invalid-feedback">{{ form.errors.company }}</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">No. WhatsApp</label>
                                        <input v-model="form.phone" type="tel" class="form-control" :class="{'is-invalid': form.errors.phone}" placeholder="08..." required>
                                        <div v-if="form.errors.phone" class="invalid-feedback">{{ form.errors.phone }}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Email</label>
                                        <input v-model="form.email" type="email" class="form-control" :class="{'is-invalid': form.errors.email}" placeholder="email@kantor.com" required>
                                        <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
                                    </div>
                                </div>

                                <hr class="my-4 text-muted opacity-25">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Jenis Kebutuhan</label>
                                        <select v-model="form.service_type" class="form-select" :class="{'is-invalid': form.errors.service_type}" required>
                                            <option value="" disabled selected>Pilih Layanan...</option>
                                            <option value="Instalasi Baru">Instalasi Baru (Gardu/Panel)</option>
                                            <option value="Maintenance">Maintenance / Perawatan Rutin</option>
                                            <option value="Troubleshooting">Perbaikan / Troubleshooting</option>
                                            <option value="Panel Maker">Pemesanan Panel (LVMDP)</option>
                                            <option value="Supply Material">Supply Material Listrik</option>
                                            <option value="Consultation">Konsultasi Desain</option>
                                        </select>
                                        <div v-if="form.errors.service_type" class="invalid-feedback">{{ form.errors.service_type }}</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Lokasi Proyek</label>
                                        <input v-model="form.location" type="text" class="form-control" :class="{'is-invalid': form.errors.location}" required>
                                        <div v-if="form.errors.location" class="invalid-feedback">{{ form.errors.location }}</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Lampiran (Opsional)</label>
                                    <input @change="handleFileUpload" type="file" class="form-control" :class="{'is-invalid': form.errors.file}" accept=".pdf,.jpg,.png,.dwg">
                                    <div class="form-text">Max 5MB (PDF, JPG, PNG).</div>
                                    <div v-if="form.errors.file" class="invalid-feedback">{{ form.errors.file }}</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Detail Pesan</label>
                                    <textarea v-model="form.message" class="form-control" :class="{'is-invalid': form.errors.message}" rows="4" required></textarea>
                                    <div v-if="form.errors.message" class="invalid-feedback">{{ form.errors.message }}</div>
                                </div>

                                <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold" :disabled="form.processing">
                                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                    {{ form.processing ? 'Sedang Mengirim...' : 'Minta Penawaran' }}
                                </button>

                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </MainLayout>
</template>

<style scoped>
/* --- COLORS --- */
.text-blue { color: #002b49; }
.section-title { color: #002b49; font-weight: 800; }
.form-title { color: #002b49; font-weight: 700; }

/* --- HERO MOBILE FIX --- */
@media (max-width: 576px) {
    .hero-mobile-fix :deep(h1) {
        font-size: 1.75rem !important;
        line-height: 1.3 !important;
        margin-bottom: 0.5rem !important;
        font-weight: 700;
    }
    .hero-mobile-fix :deep(.p-5) { padding: 2rem 1.5rem !important; }
}

/* --- INFO SECTION --- */
.icon-box {
    width: 50px; height: 50px; background-color: #eef5fa; color: #00529c;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-size: 1.5rem; margin-right: 20px; flex-shrink: 0;
}
.text-box h5 { color: #333; }
.hover-blue:hover { color: #00529c !important; text-decoration: underline !important; }
.map-container iframe { display: block; }

/* --- FORM SECTION --- */
.form-wrapper { background-color: #fff; border-top: 5px solid #00529c; }
.form-control, .form-select {
    padding: 12px; border-radius: 8px; border: 1px solid #dee2e6; transition: all 0.3s;
}
.form-control:focus, .form-select:focus {
    border-color: #00529c; box-shadow: 0 0 0 3px rgba(0, 82, 156, 0.1);
}
.btn-primary-custom {
    background-color: #002b49; color: #fff; border: none; border-radius: 8px; transition: all 0.3s;
}
.btn-primary-custom:hover {
    background-color: #e63946; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(230, 57, 70, 0.3);
}
.btn-primary-custom:disabled {
    background-color: #ccc; cursor: not-allowed; transform: none; box-shadow: none;
}

/* Responsive */
@media (max-width: 768px) {
    .form-wrapper { padding: 30px !important; }
}
</style>