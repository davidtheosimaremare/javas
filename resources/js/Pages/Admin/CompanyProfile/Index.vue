<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    profile: Object
})

// --- HELPER: RESOLVE IMAGE URL ---
// Fungsi pintar untuk menentukan sumber gambar
const resolveImageUrl = (path) => {
    if (!path) return null; // Jika kosong

    // 1. Jika file baru di-upload (Blob URL)
    if (path.startsWith('blob:')) return path;

    // 2. Jika path mengandung "images/" (berarti dari folder Public/Seeder Anda)
    //    Atau jika path adalah URL lengkap (http...)
    if (path.includes('images/') || path.startsWith('http')) {
        return path; 
    }

    // 3. Jika tidak (berarti hasil Upload Controller), tambahkan prefix storage
    return `/storage/${path}`;
}

// --- STATE PREVIEW ---
// Langsung ambil dari database melalui props
const logoPrimaryPreview = ref(resolveImageUrl(props.profile?.logo_primary))
const logoSecondaryPreview = ref(resolveImageUrl(props.profile?.logo_secondary))

// --- FORM INIT ---
const form = useForm({
    _method: 'POST', 
    company_name: props.profile?.company_name || '',
    company_description: props.profile?.company_description || '',
    company_email: props.profile?.company_email || '',
    company_address: props.profile?.company_address || '',
    phone_number: props.profile?.phone_number || '',
    logo_primary: null, // File fisik (untuk dikirim ke backend)
    logo_secondary: null,
    social_media: {
        facebook: props.profile?.social_media?.facebook || '',
        instagram: props.profile?.social_media?.instagram || '',
        linkedin: props.profile?.social_media?.linkedin || '',
        twitter: props.profile?.social_media?.twitter || '',
    }
})

// --- HANDLERS ---
const handleLogoPrimary = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.logo_primary = file
        // Buat preview lokal langsung
        logoPrimaryPreview.value = URL.createObjectURL(file)
    }
}

const handleLogoSecondary = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.logo_secondary = file
        // Buat preview lokal langsung
        logoSecondaryPreview.value = URL.createObjectURL(file)
    }
}

const submit = () => {
    // FIX: Gunakan URL manual string, bukan route()
    form.post('/admin/company-profile', {
        preserveScroll: true,
        forceFormData: true, // SANGAT PENTING: Wajib true karena ada upload file (logo)
        onSuccess: () => {
            // Opsional: Reset input file agar bersih (preview tetap ada)
            // form.reset('logo_primary', 'logo_secondary') 
            console.log("Berhasil disimpan!")
        },
        onError: (errors) => {
            console.error("Gagal simpan:", errors)
        }
    })
}

</script>

<template>
    <Head title="Company Profile" />

    <AdminLayout>
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5">
            <div>
                <h4 class="fw-bold text-slate-800 mb-1">Company Profile</h4>
                <p class="text-muted mb-0">Kelola informasi utama perusahaan, branding, dan kontak.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <button @click="submit" class="btn btn-navy rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2" :disabled="form.processing">
                    <span v-if="form.processing" class="spinner-border spinner-border-sm"></span>
                    <i v-else class="bi bi-floppy"></i>
                    Simpan Perubahan
                </button>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-lg-4">
                
                <div class="content-card mb-4">
                    <h6 class="card-title">Branding & Logo</h6>
                    
                    <div class="mb-4 text-center">
                        <label class="form-label small fw-bold text-muted text-uppercase">Logo Utama (Light/White)</label>
                        <div class="logo-preview-box bg-light  mb-2">
                            <img v-if="logoPrimaryPreview" :src="logoPrimaryPreview" class="img-fluid logo-img">
                            <span v-else class="text-white-50 small">Belum ada logo</span>
                        </div>
                        <input type="file" class="form-control form-control-sm" @change="handleLogoPrimary" accept="image/*">
                        <small class="text-muted d-block mt-1" style="font-size: 0.75rem">Format: PNG Transparan (Disarankan)</small>
                    </div>

                    <hr class="border-light my-3">

                    <div class="mb-2 text-center">
                        <label class="form-label small fw-bold text-muted text-uppercase">Logo Sekunder (Dark/Color)</label>
                        <div class="logo-preview-box bg-navy mb-2 border">
                            <img v-if="logoSecondaryPreview" :src="logoSecondaryPreview" class="img-fluid logo-img">
                            <span v-else class="text-muted small">Belum ada logo</span>
                        </div>
                        <input type="file" class="form-control form-control-sm" @change="handleLogoSecondary" accept="image/*">
                    </div>
                </div>

                <div class="content-card">
                    <h6 class="card-title">Media Sosial</h6>
                    
                    <div class="mb-3 input-group-icon">
                        <span class="icon-span text-facebook"><i class="bi bi-facebook"></i></span>
                        <input v-model="form.social_media.facebook" type="text" class="form-control ps-5" placeholder="URL Facebook">
                    </div>
                    
                    <div class="mb-3 input-group-icon">
                        <span class="icon-span text-instagram"><i class="bi bi-instagram"></i></span>
                        <input v-model="form.social_media.instagram" type="text" class="form-control ps-5" placeholder="URL Instagram">
                    </div>

                    <div class="mb-3 input-group-icon">
                        <span class="icon-span text-linkedin"><i class="bi bi-linkedin"></i></span>
                        <input v-model="form.social_media.linkedin" type="text" class="form-control ps-5" placeholder="URL LinkedIn">
                    </div>

                    <div class="mb-0 input-group-icon">
                        <span class="icon-span text-dark"><i class="bi bi-twitter-x"></i></span>
                        <input v-model="form.social_media.twitter" type="text" class="form-control ps-5" placeholder="URL X (Twitter)">
                    </div>
                </div>

            </div>

            <div class="col-lg-8">
                <div class="content-card h-100">
                    <h6 class="card-title mb-4">Informasi Umum</h6>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold text-navy">Nama Perusahaan</label>
                            <input v-model="form.company_name" type="text" class="form-control" placeholder="PT JBB ...">
                            <div v-if="form.errors.company_name" class="text-danger small mt-1">{{ form.errors.company_name }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-navy">Email Resmi</label>
                            <input v-model="form.company_email" type="email" class="form-control" placeholder="info@jbb.co.id">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-navy">Nomor Telepon / WhatsApp</label>
                            <input v-model="form.phone_number" type="text" class="form-control" placeholder="+62...">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-navy">Alamat Lengkap</label>
                            <textarea v-model="form.company_address" class="form-control" rows="3" placeholder="Alamat kantor pusat..."></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-navy">Tentang Perusahaan (Singkat)</label>
                            <textarea v-model="form.company_description" class="form-control" rows="5" placeholder="Deskripsi singkat untuk footer atau meta description..."></textarea>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end d-md-none">
                         <button @click="submit" class="btn btn-navy w-100 rounded-pill py-2" :disabled="form.processing">
                            Simpan Perubahan
                        </button>
                    </div>

                </div>
            </div>

        </div>

    </AdminLayout>
</template>

<style scoped>
/* --- COLORS --- */
.text-navy { color: #002b49; }
.text-slate-800 { color: #1e293b; }
.btn-navy {
    background-color: #002b49; color: white; border: none; font-weight: 600;
    transition: all 0.3s;
}
.btn-navy:hover { background-color: #00406b; transform: translateY(-2px); }
.btn-navy:disabled { background-color: #94a3b8; cursor: not-allowed; transform: none; }

/* --- CARDS (NOVA STYLE) --- */
.content-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    border: 1px solid rgba(0,0,0,0.05);
}

.card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #334155;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f1f5f9;
}

/* --- FORMS --- */
.form-label {
    font-size: 0.85rem;
    margin-bottom: 0.4rem;
}

.form-control {
    background-color: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 0.65rem 1rem;
    font-size: 0.95rem;
    color: #334155;
    transition: all 0.2s;
}

.form-control:focus {
    background-color: #fff;
    border-color: #002b49;
    box-shadow: 0 0 0 3px rgba(0, 43, 73, 0.1);
    outline: none;
}

/* --- LOGO PREVIEW --- */
.logo-preview-box {
    width: 100%;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    overflow: hidden;
}
.bg-navy { background-color: #002b49 !important; }
.logo-img {
    max-height: 80px;
    max-width: 80%;
    object-fit: contain;
}

/* --- SOCIAL INPUTS --- */
.input-group-icon {
    position: relative;
}
.icon-span {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.1rem;
    z-index: 5;
}
.text-facebook { color: #1877f2; }
.text-instagram { color: #e4405f; }
.text-linkedin { color: #0a66c2; }

/* Responsive */
@media (max-width: 768px) {
    .content-card { padding: 1.25rem; }
}
</style>