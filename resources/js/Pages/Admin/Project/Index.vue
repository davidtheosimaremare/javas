<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Swal from 'sweetalert2'
// Import Cropper
import { Cropper, RectangleStencil } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'

const props = defineProps({
    pageSetting: Object
})

// --- CROPPER STATE ---
const showCropperModal = ref(false)
const cropperImgSrc = ref(null)
const cropperRef = ref(null)
const previewHeroBg = ref(props.pageSetting?.hero_bg_path ? getImgUrl(props.pageSetting.hero_bg_path) : null)

// --- FORM SETTINGS ---
const formSettings = useForm({
    _method: 'POST',
    // 1. Hero Section
    hero_title: props.pageSetting?.hero_title || 'Proyek & Referensi',
    hero_bg_path: null,

    // 2. Project List Section (Text Only)
    project_title: props.pageSetting?.project_title || 'Daftar Proyek Terkini',
    project_description: props.pageSetting?.project_description || '',

    // 3. Map Section (Text Only)
    map_title: props.pageSetting?.map_title || 'Peta Sebaran Proyek',
    map_description: props.pageSetting?.map_description || '',
})

// --- HELPER UNTUK GAMBAR ---
function getImgUrl(path) {
    if (!path) return '/images/no-image.png'; 
    if (path.startsWith('http') || path.startsWith('blob:')) return path;
    const cleanPath = path.replace(/^\/?storage\//, '');
    return `/storage/${cleanPath}?t=${new Date().getTime()}`;
}

// --- CROPPER LOGIC (16:9 Only) ---
const handleCropUpload = (e) => {
    const file = e.target.files[0]; 
    if(file) { 
        const reader = new FileReader();
        reader.onload = (event) => { 
            cropperImgSrc.value = event.target.result; 
            showCropperModal.value = true; 
            e.target.value = null; // Reset input agar bisa pilih file sama ulang
        }
        reader.readAsDataURL(file)
    }
}

const confirmCrop = () => {
    const { canvas } = cropperRef.value.getResult();
    if (canvas) {
        canvas.toBlob((blob) => { 
            // 1. Buat URL Preview
            const url = URL.createObjectURL(blob);
            previewHeroBg.value = url; 
            
            // 2. Masukkan ke Form sebagai File (untuk diupload ke Laravel)
            // Kita beri nama file manual agar terbaca sebagai file gambar valid
            const file = new File([blob], "hero_bg_cropped.jpg", { type: "image/jpeg" });
            formSettings.hero_bg_path = file; 
            
            cancelCrop();
        }, 'image/jpeg', 0.9) // Quality 0.9
    }
}

const cancelCrop = () => { 
    showCropperModal.value = false; 
    cropperImgSrc.value = null 
}

// --- SUBMIT HANDLER ---
const submitSettings = () => {
    formSettings.post('/admin/project-editor/page-setting', { 
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Pengaturan halaman berhasil disimpan!',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            })
        }
    })
}
</script>

<template>
    <Head title="Editor Halaman Proyek" />

    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-slate-800 mb-0">Editor Halaman Proyek</h3>
                <p class="text-muted small mb-0">Atur judul, deskripsi, dan gambar header halaman proyek.</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 border-start border-5 border-navy">
            <h6 class="fw-bold text-navy mb-4">Form Pengaturan Halaman</h6>
            
            <form @submit.prevent="submitSettings">
                <div class="row g-4">
                    
                    <div class="col-12">
                        <div class="p-4 bg-light rounded-4 border">
                            <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">
                                <i class="bi bi-image me-2"></i>Header Utama (Hero)
                            </h6>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">JUDUL UTAMA (HERO TITLE)</label>
                                    <input v-model="formSettings.hero_title" type="text" class="form-control fw-bold" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">BACKGROUND IMAGE (16:9)</label>
                                    
                                    <div class="d-flex align-items-center gap-3 bg-white p-2 rounded border">
                                        <div class="rounded overflow-hidden bg-secondary position-relative" style="width: 120px; height: 67px;">
                                            <img v-if="previewHeroBg" :src="previewHeroBg" class="w-100 h-100 object-fit-cover">
                                            <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center text-white-50 small">No Img</div>
                                        </div>
                                        
                                        <div class="flex-grow-1">
                                            <input @change="handleCropUpload" type="file" class="form-control form-control-sm" accept="image/*">
                                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                                Otomatis membuka alat potong (crop) 16:9.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-4 bg-white rounded-4 border h-100 shadow-sm">
                            <h6 class="fw-bold text-navy mb-3">
                                <i class="bi bi-grid me-2"></i>Seksi Daftar Proyek
                            </h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">JUDUL SEKSI</label>
                                <input v-model="formSettings.project_title" type="text" class="form-control" placeholder="Contoh: Daftar Proyek Terkini">
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted">DESKRIPSI SEKSI</label>
                                <textarea v-model="formSettings.project_description" class="form-control" rows="3" placeholder="Contoh: Menampilkan hasil karya..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-4 bg-white rounded-4 border h-100 shadow-sm">
                            <h6 class="fw-bold text-navy mb-3">
                                <i class="bi bi-map me-2"></i>Seksi Peta Sebaran
                            </h6>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">JUDUL SEKSI</label>
                                <input v-model="formSettings.map_title" type="text" class="form-control" placeholder="Contoh: Peta Sebaran Proyek">
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted">DESKRIPSI SEKSI</label>
                                <textarea v-model="formSettings.map_description" class="form-control" rows="3" placeholder="Contoh: Jangkauan layanan kami..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-end mt-2">
                        <button type="submit" class="btn btn-navy rounded-pill px-5 py-2 shadow" :disabled="formSettings.processing">
                            <span v-if="formSettings.processing" class="spinner-border spinner-border-sm me-1"></span>
                            <i v-else class="bi bi-save me-2"></i> Simpan Semua Pengaturan
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </AdminLayout>

    <div v-if="showCropperModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3" style="z-index: 1060;">
        <div class="modal-content-custom bg-white rounded-4 shadow-lg w-100 overflow-hidden" style="max-width: 600px;">
            <div class="modal-header px-4 py-3 bg-navy text-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold m-0">
                    <i class="bi bi-crop me-2"></i>Potong Background (16:9)
                </h6>
                <button @click="cancelCrop" class="btn-close btn-close-white"></button>
            </div>
            <div class="modal-body p-0 bg-dark">
                 <cropper 
                    ref="cropperRef" 
                    class="cropper-container" 
                    :src="cropperImgSrc" 
                    :stencil-component="RectangleStencil" 
                    :stencil-props="{ aspectRatio: 16/9 }" 
                    image-class="cropper-image-contain"
                />
            </div>
            <div class="modal-footer p-3 bg-white d-flex justify-content-end gap-2">
                <button @click="cancelCrop" class="btn btn-light border rounded-pill">Batal</button>
                <button @click="confirmCrop" class="btn btn-navy rounded-pill px-4"><i class="bi bi-check-lg me-2"></i> Potong & Gunakan</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* CUSTOM STYLES */
.nova-card { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
.text-navy { color: #002b49; }
.bg-navy { background-color: #002b49; }
.btn-navy { background-color: #002b49; color: #fff; border:none; transition: 0.3s; }
.btn-navy:hover { background-color: #00406b; }
.form-control:focus { border-color: #002b49; box-shadow: 0 0 0 3px rgba(0, 43, 73, 0.1); }

/* CROPPER STYLE */
.modal-backdrop-custom { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1050; }
.modal-content-custom { animation: slideUp 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.cropper-container { height: 400px; background-color: #000; }
:deep(.cropper-image-contain) { object-fit: contain; }
</style>