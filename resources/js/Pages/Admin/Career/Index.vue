<script setup>
import { ref, watch } from 'vue' 
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import draggable from 'vuedraggable'
import Swal from 'sweetalert2'
import { Cropper, RectangleStencil } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'

const props = defineProps({
    pageSetting: Object,
    coreValues: Array
})

// --- STATE ---
const activeTab = ref('settings')
const isModalOpen = ref(false)
const editingItem = ref(null)
const previewImage = ref(null) // Preview Modal Core Value

// Local State untuk Drag & Drop
const valuesList = ref([...props.coreValues])

// Watcher agar data sync saat update/delete berhasil
watch(() => props.coreValues, (newVal) => { 
    valuesList.value = [...newVal] 
}, { deep: true })

// --- CROPPER STATE ---
const showCropperModal = ref(false)
const cropperImgSrc = ref(null)
const cropperRef = ref(null)
const cropperType = ref('hero') // 'hero' | 'value'
const previewHeroBg = ref(props.pageSetting?.hero_bg_path ? getImgUrl(props.pageSetting.hero_bg_path) : null)

// --- FORMS ---

// 1. Form Hero & Settings (LENGKAP DENGAN TEXT BARU)
const formSettings = useForm({
    _method: 'POST',
    // Hero
    hero_title: props.pageSetting?.hero_title || 'Karir & Budaya',
    hero_bg_path: null, 
    
    // Intro Section
    career_intro_title: props.pageSetting?.career_intro_title || '',
    career_intro_desc: props.pageSetting?.career_intro_desc || '',

    // Values Section Header
    career_values_title: props.pageSetting?.career_values_title || '',
    career_values_subtitle: props.pageSetting?.career_values_subtitle || '',

    // Jobs Section Header
    career_jobs_title: props.pageSetting?.career_jobs_title || '',
    career_jobs_subtitle: props.pageSetting?.career_jobs_subtitle || '',
})

// 2. Form Modal Core Value
const formValue = useForm({
    _method: 'POST',
    title: '',
    description: '',
    image: null, // File upload
    order: 0
})

// --- HELPERS ---
function getImgUrl(path) {
    if (!path) return '/images/no-image.png'; 
    if (path.startsWith('http') || path.startsWith('blob:')) return path;
    const cleanPath = path.replace(/^\/?storage\//, '');
    return `/storage/${cleanPath}?t=${new Date().getTime()}`;
}

const showSuccessToast = (msg) => {
    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: msg, showConfirmButton: false, timer: 3000 })
}

// --- CROPPER LOGIC ---
const handleCropUpload = (e, type) => {
    const file = e.target.files[0]; 
    if(file) { 
        cropperType.value = type; 
        const reader = new FileReader();
        reader.onload = (event) => { 
            cropperImgSrc.value = event.target.result; 
            showCropperModal.value = true; 
            e.target.value = null 
        }
        reader.readAsDataURL(file)
    }
}

const confirmCrop = () => {
    const { canvas } = cropperRef.value.getResult();
    if (canvas) {
        canvas.toBlob((blob) => { 
            const url = URL.createObjectURL(blob);
            const file = new File([blob], "cropped_image.jpg", { type: "image/jpeg" });

            if (cropperType.value === 'hero') {
                formSettings.hero_bg_path = file; 
                previewHeroBg.value = url; 
            } else {
                formValue.image = file;
                previewImage.value = url;
            }
            
            cancelCrop();
        }, 'image/jpeg', 0.9)
    }
}
const cancelCrop = () => { showCropperModal.value = false; cropperImgSrc.value = null }


// --- ACTIONS ---

// Submit Settings
const submitSettings = () => {
    formSettings.post('/admin/career-editor/page-setting', { 
        preserveScroll: true, forceFormData: true, 
        onSuccess: () => showSuccessToast('Settings disimpan!') 
    })
}

// Modal Logic Core Value
const openModal = (item = null) => {
    editingItem.value = item
    formValue.clearErrors(); 
    formValue.reset(); 
    previewImage.value = null;
    
    // Method Spoofing
    formValue._method = item ? 'PUT' : 'POST';

    if (item) {
        // Edit Mode
        formValue.title = item.title || ''
        formValue.description = item.description || ''
        formValue.order = item.order || 0
        
        // [FIX] Menggunakan 'item.image' sesuai database controller Anda
        if (item.image) {
            previewImage.value = getImgUrl(item.image)
        }
    }
    isModalOpen.value = true
}

const closeModal = () => { isModalOpen.value = false; editingItem.value = null }

const submitValue = () => {
    const url = editingItem.value 
        ? `/admin/career-editor/value/${editingItem.value.id}` 
        : '/admin/career-editor/value'
        
    formValue.post(url, { 
        preserveScroll: true, forceFormData: true, 
        onSuccess: () => { 
            closeModal();
            showSuccessToast('Core Value disimpan!');
        } 
    })
}

const deleteValue = (id) => {
    Swal.fire({ title: 'Hapus item ini?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!' })
    .then((result) => {
        if (result.isConfirmed) {
            router.delete(`/admin/career-editor/value/${id}`, { 
                preserveScroll: true,
                onSuccess: () => showSuccessToast('Item dihapus.')
            })
        }
    })
}

// --- DRAG CHANGE HANDLER ---
const onDragChange = () => {
    router.post('/admin/career-editor/value/reorder', { 
        items: valuesList.value 
    }, { 
        preserveScroll: true,
        onSuccess: () => showSuccessToast('Urutan disimpan.')
    })
}
</script>

<template>
    <Head title="Manajemen Karir" />

    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-slate-800 mb-0">Manajemen Karir</h3>
        </div>

        <div class="nav-tabs-wrapper mb-4">
            <button class="tab-btn" :class="{ active: activeTab === 'settings' }" @click="activeTab = 'settings'">
                <i class="bi bi-sliders me-2"></i> Hero & Settings
            </button>
            <button class="tab-btn" :class="{ active: activeTab === 'values' }" @click="activeTab = 'values'">
                <i class="bi bi-gem me-2"></i> Core Values
            </button>
        </div>

        <div v-if="activeTab === 'settings'">
            <div class="nova-card p-4 border-start border-5 border-navy">
                <h6 class="fw-bold text-navy mb-3">Pengaturan Halaman Karir</h6>
                
                <form @submit.prevent="submitSettings">
                    <div class="row g-4">
                        
                        <div class="col-12">
                            <div class="p-3 bg-light rounded border">
                                <h6 class="fw-bold text-primary mb-3">#1 Hero Header</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">HERO TITLE</label>
                                        <input v-model="formSettings.hero_title" type="text" class="form-control fw-bold" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">BACKGROUND BANNER (16:9)</label>
                                        <div class="d-flex align-items-center gap-3 bg-white p-2 rounded border">
                                            <div class="preview-box border rounded overflow-hidden bg-secondary position-relative" style="width: 120px; height: 67px;">
                                                <img v-if="previewHeroBg" :src="previewHeroBg" class="w-100 h-100 object-fit-cover">
                                                <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center text-white-50 small">No Img</div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <input @change="(e) => handleCropUpload(e, 'hero')" type="file" class="form-control form-control-sm" accept="image/*">
                                                <small class="text-muted d-block mt-1">Otomatis membuka crop tool 16:9.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 bg-white border rounded">
                                <h6 class="fw-bold text-navy mb-3">#2 Seksi Intro (Sambutan)</h6>
                                <div class="mb-3">
                                    <label class="form-label small text-muted">JUDUL INTRO</label>
                                    <input v-model="formSettings.career_intro_title" type="text" class="form-control">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small text-muted">DESKRIPSI INTRO</label>
                                    <textarea v-model="formSettings.career_intro_desc" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-white border rounded h-100">
                                <h6 class="fw-bold text-navy mb-3">#3 Header Core Values</h6>
                                <div class="mb-3">
                                    <label class="form-label small text-muted">JUDUL</label>
                                    <input v-model="formSettings.career_values_title" type="text" class="form-control">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small text-muted">SUBTITLE</label>
                                    <input v-model="formSettings.career_values_subtitle" type="text" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-white border rounded h-100">
                                <h6 class="fw-bold text-navy mb-3">#4 Header Lowongan Kerja</h6>
                                <div class="mb-3">
                                    <label class="form-label small text-muted">JUDUL</label>
                                    <input v-model="formSettings.career_jobs_title" type="text" class="form-control">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small text-muted">SUBTITLE</label>
                                    <input v-model="formSettings.career_jobs_subtitle" type="text" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-navy rounded-pill px-5" :disabled="formSettings.processing">
                                Simpan Semua Pengaturan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="activeTab === 'values'">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-navy m-0">Daftar Nilai Kerja (Core Values)</h6>
                
                <div class="d-flex align-items-center gap-3">
                    <small class="text-muted fst-italic"><i class="bi bi-arrows-move"></i> Drag untuk urutkan</small>
                    
                    <button v-if="valuesList.length < 4" @click="openModal()" class="btn btn-sm btn-outline-navy rounded-pill">
                        <i class="bi bi-plus-lg"></i> Tambah Value
                    </button>
                    <span v-else class="badge bg-secondary text-white px-3 py-2 rounded-pill">
                        <i class="bi bi-check-circle-fill me-1"></i> Maksimal 4 Item
                    </span>
                </div>
            </div>

            <draggable 
                v-model="valuesList" 
                item-key="id" 
                class="row g-4" 
                @change="onDragChange" 
                ghost-class="ghost-card"
            >
                <template #item="{ element }">
                    <div class="col-md-6 col-lg-3 draggable-item">
                        <div class="nova-card h-100 text-center p-4 position-relative group-hover-container cursor-move">
                            <div class="mx-auto mb-3 rounded overflow-hidden shadow-sm bg-light" style="width: 100%; height: 200px;">
                                <img :src="getImgUrl(element.image)" class="w-100 h-100 object-fit-cover">
                            </div>
                            
                            <h6 class="fw-bold text-dark mb-2">{{ element.title }}</h6>
                            <p class="text-muted small mb-3 text-truncate-3">{{ element.description }}</p>

                            <div class="mt-auto pt-3 border-top border-light d-flex justify-content-between align-items-center">
                                <span class="badge bg-light text-dark border">#{{ element.order }}</span>
                                <div class="d-flex gap-2">
                                    <button @click="openModal(element)" class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                                    <button @click="deleteValue(element.id)" class="btn btn-sm btn-light border text-danger"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </draggable>
            
            <div v-if="valuesList.length === 0" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                Belum ada Core Value. Silakan tambahkan.
            </div>
        </div>

    </AdminLayout>

    <div v-if="isModalOpen" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3">
        <div class="modal-content-custom bg-white rounded-4 shadow-lg w-100" style="max-width: 500px;">
            <div class="modal-header px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0 text-navy">{{ editingItem ? 'Edit' : 'Tambah' }} Core Value</h5>
                <button @click="closeModal" class="btn-close"></button>
            </div>
            <div class="modal-body p-4">
                <form @submit.prevent="submitValue">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">JUDUL VALUE</label>
                        <input v-model="formValue.title" type="text" class="form-control" placeholder="Contoh: Integrity" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">DESKRIPSI</label>
                        <textarea v-model="formValue.description" class="form-control" rows="3" placeholder="Penjelasan singkat..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">GAMBAR ILUSTRASI</label>
                        <div class="d-flex align-items-center gap-3 border rounded p-2 bg-light">
                            <div class="preview-box border rounded overflow-hidden bg-white position-relative" style="width: 80px; height: 100px;">
                                <img v-if="previewImage" :src="previewImage" class="w-100 h-100 object-fit-cover">
                                <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
                            </div>
                            <div class="flex-grow-1">
                                <input @change="(e) => handleCropUpload(e, 'value')" type="file" class="form-control form-control-sm" accept="image/*">
                                <small class="text-muted d-block mt-1">Rekomendasi: Gambar Portrait / 4:5.</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-end pt-3 border-top">
                        <button type="submit" class="btn btn-navy rounded-pill px-4" :disabled="formValue.processing">Simpan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div v-if="showCropperModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3" style="z-index: 1060;">
        <div class="modal-content-custom bg-white rounded-4 shadow-lg w-100 overflow-hidden" style="max-width: 600px;">
            <div class="modal-header px-4 py-3 bg-navy text-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold m-0">
                    {{ cropperType === 'hero' ? 'Potong Background (16:9)' : 'Potong Gambar Value (4:5)' }}
                </h6>
                <button @click="cancelCrop" class="btn-close btn-close-white"></button>
            </div>
            <div class="modal-body p-0 bg-dark">
                 <cropper 
                    ref="cropperRef" 
                    class="cropper-container" 
                    :src="cropperImgSrc" 
                    :stencil-component="RectangleStencil" 
                    :stencil-props="{ aspectRatio: cropperType === 'hero' ? 16/9 : 4/5 }" 
                    image-class="cropper-image-contain"
                />
            </div>
            <div class="modal-footer p-3 bg-white d-flex justify-content-end gap-2">
                <button @click="cancelCrop" class="btn btn-light border rounded-pill">Batal</button>
                <button @click="confirmCrop" class="btn btn-navy rounded-pill px-4"><i class="bi bi-crop me-2"></i> Potong & Gunakan</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Reuse Styles */
.cursor-move { cursor: move; }
.ghost-card { opacity: 0.5; background: #e0f2fe; border: 2px dashed #002b49; }

.nav-tabs-wrapper { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px; }
.tab-btn { border: 1px solid #e2e8f0; background: #fff; color: #64748b; padding: 10px 20px; border-radius: 50px; font-weight: 600; font-size: 0.9rem; white-space: nowrap; transition: 0.3s; }
.tab-btn.active { background-color: #002b49; color: #fff; border-color: #002b49; box-shadow: 0 4px 6px rgba(0, 43, 73, 0.2); }

.nova-card { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); transition: transform 0.2s; }
.nova-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
.text-navy { color: #002b49; }
.btn-navy { background-color: #002b49; color: #fff; border:none; transition: 0.3s; }
.btn-navy:hover { background-color: #00406b; }
.btn-outline-navy { border: 1px solid #002b49; color: #002b49; transition: 0.3s; }
.btn-outline-navy:hover { background-color: #002b49; color: #fff; }

.modal-backdrop-custom { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1050; }
.modal-content-custom { animation: slideUp 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

.object-fit-cover { object-fit: cover; }
.form-control:focus { border-color: #002b49; box-shadow: 0 0 0 3px rgba(0, 43, 73, 0.1); }
.text-truncate-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

/* CROPPER STYLE */
.cropper-container { height: 400px; background-color: #000; }
:deep(.cropper-image-contain) { object-fit: contain; }
</style>