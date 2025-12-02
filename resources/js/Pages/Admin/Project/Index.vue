<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    pageSetting: Object
})

// --- FORMS ---
const form = useForm({
    _method: 'POST',
    hero_title: props.pageSetting?.hero_title || 'Proyek & Referensi',
    hero_bg_path: null, // File input
})

// Preview Image
const previewHeroBg = ref(props.pageSetting?.hero_bg_path ? getImgUrl(props.pageSetting.hero_bg_path) : null)

// --- HELPERS ---
function getImgUrl(path) {
    if (!path) return '/images/no-image.png'; 
    if (path.startsWith('http') || path.startsWith('blob:')) return path;
    if (path.includes('defaults/')) return `/images/${path.replace(/^\/?storage\//, '')}`; 
    const cleanPath = path.replace(/^\/?storage\//, '');
    return `/storage/${cleanPath}?t=${new Date().getTime()}`;
}

// --- ACTIONS ---
const handleHeroUpload = (e) => {
    const file = e.target.files[0]; 
    if(file) { 
        form.hero_bg_path = file; 
        previewHeroBg.value = URL.createObjectURL(file); 
    }
}

const submitSettings = () => {
    form.post('/admin/project-editor/page-setting', {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => { /* Optional Toast */ }
    })
}
</script>

<template>
    <Head title="Manajemen Halaman Proyek" />

    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-slate-800 mb-0">Manajemen Halaman Proyek</h3>
        </div>

        <div class="nova-card p-4 border-start border-5 border-navy">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="fw-bold text-navy m-0">Pengaturan Hero Page</h6>
                    <small class="text-muted">Mengatur tampilan banner atas di halaman Daftar Proyek.</small>
                </div>
            </div>

            <form @submit.prevent="submitSettings">
                <div class="row g-4">
                    
                    <div class="col-md-12">
                        <label class="form-label small fw-bold text-muted">HERO TITLE (JUDUL HALAMAN)</label>
                        <input 
                            v-model="form.hero_title" 
                            type="text" 
                            class="form-control form-control-lg fw-bold text-navy" 
                            placeholder="Contoh: Proyek & Referensi"
                            required
                        >
                    </div>

                    <div class="col-md-12">
                        <label class="form-label small fw-bold text-muted">BACKGROUND BANNER (16:9)</label>
                        
                        <div class="d-flex align-items-center gap-4 border rounded p-3 bg-light">
                            <div class="preview-box border rounded overflow-hidden bg-secondary flex-shrink-0" style="width: 240px; height: 135px;"> <img v-if="previewHeroBg" :src="previewHeroBg" class="w-100 h-100 object-fit-cover">
                                <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center text-white-50 small">
                                    <i class="bi bi-image fs-1"></i>
                                </div>
                            </div>

                            <div class="flex-grow-1">
                                <input 
                                    @change="handleHeroUpload" 
                                    type="file" 
                                    class="form-control mb-2" 
                                    accept="image/*"
                                >
                                <ul class="text-muted small mb-0 ps-3">
                                    <li>Format: JPG, PNG, WEBP.</li>
                                    <li>Ukuran Rekomendasi: <strong>1920 x 600 px</strong> (Landscape).</li>
                                    <li>Gambar ini akan menjadi background header di halaman <code>/projects</code>.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-end pt-3 border-top">
                        <button type="submit" class="btn btn-navy rounded-pill px-5 py-2" :disabled="form.processing">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>
                    </div>

                </div>
            </form>
        </div>

        <div class="alert alert-info mt-4 d-flex align-items-center shadow-sm border-0 rounded-3">
            <i class="bi bi-info-circle-fill fs-4 me-3 text-navy"></i>
            <div>
                <strong>Catatan:</strong> Halaman ini hanya untuk mengedit tampilan <strong>Header/Hero</strong>. 
                <br>Untuk menambah atau mengedit data proyek itu sendiri, silakan gunakan menu <strong>Manajemen Proyek</strong> di sidebar.
            </div>
        </div>

    </AdminLayout>
</template>

<style scoped>
/* Reuse Styles */
.nova-card { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
.text-navy { color: #002b49; }
.btn-navy { background-color: #002b49; color: #fff; border:none; transition: 0.3s; }
.btn-navy:hover { background-color: #00406b; transform: translateY(-2px); }
.btn-navy:disabled { background-color: #94a3b8; transform: none; }

.object-fit-cover { object-fit: cover; }
.form-label { font-size: 0.75rem; letter-spacing: 0.5px; }
.form-control { background-color: #f8fafc; border: 1px solid #cbd5e1; }
.form-control:focus { background-color: #fff; border-color: #002b49; box-shadow: 0 0 0 3px rgba(0, 43, 73, 0.1); }
</style>