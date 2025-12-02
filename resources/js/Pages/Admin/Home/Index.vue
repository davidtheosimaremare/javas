<script setup>
import { ref, watch, computed } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import draggable from 'vuedraggable'
import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'

const props = defineProps({
    sliders: Array,
    services: Array,
    statistics: Array,
    pageSetting: Object,
    projects: Array
})

// State Tab & Data
const sliderList = ref([...props.sliders])
const activeTab = ref('slider')
const projectList = ref([...props.projects])

// Watcher Sync
watch(() => props.sliders, (newVal) => { sliderList.value = [...newVal] }, { deep: true })
watch(() => props.projects, (newVal) => { projectList.value = [...newVal] }, { deep: true })

// Pagination Proyek
const currentPage = ref(1)
const itemsPerPage = 10
const totalPages = computed(() => Math.ceil(projectList.value.length / itemsPerPage))
const paginatedProjects = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage
    const end = start + itemsPerPage
    return projectList.value.slice(start, end)
})
const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }

// Modal & Cropper State
const isModalOpen = ref(false)
const modalType = ref('')
const editingItem = ref(null)
const previewImage = ref(null)
const showCropperModal = ref(false)
const cropperImgSrc = ref(null)
const cropperRef = ref(null)

// --- FORMS ---
const form = useForm({
    _method: 'POST',
    title: '', description: '', nav_title: '', image: null, link: '', 
    color: '', value: '', label: '', order: 0, is_active: 1
})

const formHeader = useForm({
    service_title: props.pageSetting?.service_title || 'Jelajahi Ekosistem JBB',
    service_description: props.pageSetting?.service_description || 'Kami adalah mitra strategis Anda...',
})

const formStatsHeader = useForm({
    stats_title: props.pageSetting?.stats_title || 'Pencapaian Kami',
    stats_description: props.pageSetting?.stats_description || 'Angka yang membuktikan...',
    stats_bg_image: null, 
})
const previewStatsBg = ref(props.pageSetting?.stats_bg_image ? getImgUrl(props.pageSetting.stats_bg_image) : null)

const formProjectHeader = useForm({
    project_title: props.pageSetting?.project_title || 'Project Reference',
    project_description: props.pageSetting?.project_description || 'Portofolio proyek unggulan kami...',
})

// Helper URL
function getImgUrl(path) {
    if (!path) return '/images/no-image.png'; 
    if (path.startsWith('http') || path.startsWith('blob:')) return path;
    if (path.includes('defaults/')) return `/images/${path.replace(/^\/?storage\//, '')}`; 
    const cleanPath = path.replace(/^\/?storage\//, '');
    return `/storage/${cleanPath}?t=${new Date().getTime()}`;
}

// Crop Logic
const cropAspectRatio = computed(() => modalType.value === 'service' ? 9/16 : 16/9)
const cropStencilProps = computed(() => ({ aspectRatio: cropAspectRatio.value }))

// Actions
const toggleFeatured = (project) => {
    // Optimistic UI update (Ubah tampilan duluan biar cepat)
    project.is_featured = !project.is_featured
    
    // Kirim request ke backend
    router.post(`/admin/projects/${project.id}/toggle-featured`, {}, {
        preserveScroll: true,
        onSuccess: () => { 
            // Sukses, tidak perlu action apa-apa karena UI sudah berubah
            console.log('Featured Updated') 
        },
        onError: () => { 
            // Jika gagal, kembalikan status switch ke posisi semula
            project.is_featured = !project.is_featured 
            alert('Gagal mengubah status.')
        }
    })
}

const submitProjectHeader = () => { formProjectHeader.post('/admin/home-editor/project-header', { preserveScroll: true }) }
const submitHeader = () => { formHeader.post('/admin/home-editor/service-header', { preserveScroll: true }) }
const submitStatsHeader = () => { formStatsHeader.post('/admin/home-editor/stats-header', { preserveScroll: true, forceFormData: true }) }

const openEditModal = (type, item) => {
    modalType.value = type
    editingItem.value = item
    form.clearErrors(); form.reset();
    
    form.title = item.title || ''; form.description = item.description || ''; form.nav_title = item.nav_title || ''
    form.link = item.link || ''; form.color = item.color || '#002b49'; form.value = item.value || ''; form.label = item.label || ''
    form.order = item.order || 0; form.is_active = item.is_active ? 1 : 0
    form.image = null 
    
    previewImage.value = item.image ? getImgUrl(item.image) : null
    isModalOpen.value = true
}

const closeModal = () => { isModalOpen.value = false; editingItem.value = null; previewImage.value = null; }

const handleImageUpload = (e) => {
    const file = e.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (ev) => { cropperImgSrc.value = ev.target.result; showCropperModal.value = true; e.target.value = null }; reader.readAsDataURL(file) }
}
const cropImage = () => { const { canvas } = cropperRef.value.getResult(); if (canvas) { canvas.toBlob((blob) => { form.image = blob; previewImage.value = URL.createObjectURL(blob); cancelCrop() }, 'image/jpeg', 0.9) } }
const cancelCrop = () => { showCropperModal.value = false; cropperImgSrc.value = null }
const onDragChange = () => { router.post('/admin/home-editor/slider/reorder', { items: sliderList.value }, { preserveScroll: true }) }

const submitUpdate = () => {
    let url = ''
    if(modalType.value === 'slider') url = `/admin/home-editor/slider/${editingItem.value.id}`
    if(modalType.value === 'service') url = `/admin/home-editor/service/${editingItem.value.id}`
    if(modalType.value === 'stat') url = `/admin/home-editor/statistic/${editingItem.value.id}`
    form.post(url, { preserveScroll: true, forceFormData: true, onSuccess: () => closeModal() })
}
const handleStatsBgUpload = (e) => { const file = e.target.files[0]; if(file) { formStatsHeader.stats_bg_image = file; previewStatsBg.value = URL.createObjectURL(file) } }
</script>

<template>
    <Head title="Manajemen Homepage" />

    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-slate-800 mb-0">Manajemen Homepage</h3>
        </div>

        <div class="nav-tabs-wrapper mb-4">
            <button class="tab-btn" :class="{ active: activeTab === 'slider' }" @click="activeTab = 'slider'"><i class="bi bi-images me-2"></i> Slider</button>
            <button class="tab-btn" :class="{ active: activeTab === 'service' }" @click="activeTab = 'service'"><i class="bi bi-grid-fill me-2"></i> Services</button>
            <button class="tab-btn" :class="{ active: activeTab === 'stats' }" @click="activeTab = 'stats'"><i class="bi bi-bar-chart-fill me-2"></i> Statistik</button>
            <button class="tab-btn" :class="{ active: activeTab === 'project' }" @click="activeTab = 'project'"><i class="bi bi-briefcase-fill me-2"></i> Highlight Proyek</button>
        </div>

        <div v-if="activeTab === 'slider'">
            <div class="alert alert-info d-flex align-items-center mb-3 small py-2">
                <i class="bi bi-info-circle-fill me-2"></i> <span>Drag & Drop untuk urutan. Gambar wajib 16:9.</span>
            </div>
            <draggable v-model="sliderList" item-key="id" class="row g-4" @change="onDragChange" ghost-class="ghost-card">
                <template #item="{ element }">
                    <div class="col-md-6 draggable-item">
                        <div class="nova-card h-100 p-0 overflow-hidden cursor-move">
                            <div class="card-img-top position-relative" style="height: 220px;">
                                <img :src="getImgUrl(element.image)" class="w-100 h-100 object-fit-cover">
                                <div class="position-absolute top-0 start-0 bg-navy text-white px-3 py-1 rounded-end-pill mt-3 small fw-bold">#{{ element.order }}</div>
                                <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-gradient-dark text-white">
                                    <h6 class="fw-bold mb-0 text-truncate">{{ element.title }}</h6>
                                </div>
                            </div>
                            
                            <div class="p-3">
                                <p class="small text-muted mb-3 text-truncate-2" style="min-height: 38px;">
                                    {{ element.description }}
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top border-light">
                                    <span class="badge" :class="element.is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'">{{ element.is_active ? 'Active' : 'Hidden' }}</span>
                                    <button @click="openEditModal('slider', element)" class="btn btn-sm btn-navy rounded-pill px-3"><i class="bi bi-pencil"></i> Edit</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </template>
            </draggable>
        </div>

        <div v-if="activeTab === 'service'">
            <div class="nova-card mb-4 p-4 border-start border-5 border-navy">
                <h6 class="fw-bold text-navy mb-3">Edit Header Section Service</h6>
                <form @submit.prevent="submitHeader">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4"><label class="form-label small fw-bold text-muted">JUDUL</label><input v-model="formHeader.service_title" type="text" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label small fw-bold text-muted">DESKRIPSI</label><textarea v-model="formHeader.service_description" class="form-control" rows="1"></textarea></div>
                        <div class="col-md-2"><button type="submit" class="btn btn-navy w-100 rounded-pill" :disabled="formHeader.processing">Simpan</button></div>
                    </div>
                </form>
            </div>
            <div class="row g-4">
                <div v-for="item in services" :key="item.id" class="col-md-6 col-lg-3">
                    <div class="nova-card h-100 text-center">
                        <div class="mx-auto mb-3 rounded shadow-sm position-relative overflow-hidden" 
                             :style="{ borderTop: `5px solid ${item.color}`, width: '100px', height: '177px', borderRadius: '8px' }">
                            <img :src="getImgUrl(item.image)" class="w-100 h-100 object-fit-cover">
                        </div>
                        
                        <h6 class="fw-bold mb-2 text-dark">{{ item.title }}</h6>
                        
                        <div class="d-inline-flex align-items-center gap-2 mb-3 px-3 py-1 rounded-pill bg-light border">
                             <div class="rounded-circle" :style="{ backgroundColor: item.color, width: '15px', height: '15px' }"></div>
                             <small class="font-monospace">{{ item.color }}</small>
                        </div>

                        <div class="mt-auto pt-3 border-top border-light">
                            <button @click="openEditModal('service', item)" class="btn btn-sm btn-outline-navy w-100 rounded-pill">Edit Service</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'stats'">
            <div class="nova-card mb-4 p-4 border-start border-5 border-danger">
                <h6 class="fw-bold text-navy mb-3">Edit Header Statistik</h6>
                <form @submit.prevent="submitStatsHeader">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label small fw-bold text-muted">JUDUL</label><input v-model="formStatsHeader.stats_title" type="text" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label small fw-bold text-muted">DESKRIPSI</label><textarea v-model="formStatsHeader.stats_description" class="form-control" rows="1"></textarea></div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">BACKGROUND (16:9)</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="preview-box border rounded overflow-hidden bg-light" style="width: 100px; height: 60px;">
                                    <img v-if="previewStatsBg" :src="previewStatsBg" class="w-100 h-100 object-fit-cover">
                                </div>
                                <input @change="handleStatsBgUpload" type="file" class="form-control form-control-sm" accept="image/*">
                            </div>
                        </div>
                        <div class="col-12 text-end"><button type="submit" class="btn btn-sm btn-navy rounded-pill px-4" :disabled="formStatsHeader.processing">Simpan Header</button></div>
                    </div>
                </form>
            </div>
            <div class="row g-4">
                <div v-for="item in statistics" :key="item.id" class="col-md-4">
                    <div class="nova-card h-100 p-4 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="display-6 fw-bold text-navy mb-0">{{ item.value }}</div>
                            <p class="text-muted text-uppercase small fw-bold tracking-wide mb-0">{{ item.label }}</p>
                        </div>
                        <button @click="openEditModal('stat', item)" class="btn btn-icon bg-light text-navy rounded-circle"><i class="bi bi-pencil-fill"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'project'">
            <div class="nova-card mb-4 p-4 border-start border-5 border-primary">
                <h6 class="fw-bold text-navy mb-3">Edit Header Section Project</h6>
                <form @submit.prevent="submitProjectHeader">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4"><label class="form-label small fw-bold text-muted">JUDUL UTAMA</label><input v-model="formProjectHeader.project_title" type="text" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label small fw-bold text-muted">DESKRIPSI</label><textarea v-model="formProjectHeader.project_description" class="form-control" rows="1"></textarea></div>
                        <div class="col-md-2"><button type="submit" class="btn btn-navy w-100 rounded-pill" :disabled="formProjectHeader.processing">Simpan</button></div>
                    </div>
                </form>
            </div>
            <div class="nova-card">
                <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-navy">Pilih Proyek Unggulan</h6>
                    <div class="d-flex align-items-center gap-2">
                        <button @click="prevPage" class="btn btn-sm btn-light border" :disabled="currentPage === 1"><i class="bi bi-chevron-left"></i></button>
                        <span class="small text-muted">{{ currentPage }} / {{ totalPages }}</span>
                        <button @click="nextPage" class="btn btn-sm btn-light border" :disabled="currentPage === totalPages"><i class="bi bi-chevron-right"></i></button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 custom-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Nama Proyek</th>
                                <th>Klien</th>
                                <th>Status</th>
                                <th class="text-center">Featured</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="project in paginatedProjects" :key="project.id">
                                <td class="ps-4 fw-bold text-dark">{{ project.title }}</td>
                                <td class="text-muted small">{{ project.client }}</td>
                                <td>
                                    <span class="badge rounded-pill px-3 fw-normal" :class="project.status === 'Completed' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'">{{ project.status }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block">
                                        <input class="form-check-input cursor-pointer" type="checkbox" role="switch" :checked="project.is_featured" @change="toggleFeatured(project)">
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="paginatedProjects.length === 0">
                                <td colspan="4" class="text-center py-4 text-muted">Tidak ada data proyek.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </AdminLayout>

    <div v-if="isModalOpen" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3" style="z-index: 1050;">
        <div class="modal-content-custom bg-white rounded-4 shadow-lg w-100" style="max-width: 500px;">
            <div class="modal-header px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0 text-navy">Edit {{ modalType.toUpperCase() }}</h5>
                <button @click="closeModal" class="btn-close"></button>
            </div>
            <div class="modal-body p-4 custom-scroll" style="max-height: 80vh; overflow-y: auto;">
                <form @submit.prevent="submitUpdate">
                    
                    <div v-if="modalType !== 'stat'">
                        <div class="mb-3"><label class="form-label small fw-bold text-muted">JUDUL UTAMA</label><input v-model="form.title" type="text" class="form-control" required></div>
                        <div class="mb-3" v-if="modalType === 'slider'"><label class="form-label small fw-bold text-muted">NAV TITLE</label><input v-model="form.nav_title" type="text" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label small fw-bold text-muted">DESKRIPSI</label><textarea v-model="form.description" class="form-control" rows="3" required></textarea></div>
                        <div class="mb-3" v-if="modalType === 'slider'"><label class="form-label small fw-bold text-muted">LINK TOMBOL</label><input v-model="form.link" type="text" class="form-control"></div>
                    
                         <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">
                                {{ modalType === 'service' ? 'GAMBAR (Wajib 9:16 Tegak)' : 'GAMBAR (16:9)' }}
                            </label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="preview-box border rounded overflow-hidden bg-light position-relative" 
                                    :style="{ width: modalType === 'service' ? '60px' : '120px', height: modalType === 'service' ? '106px' : '67.5px' }">
                                    <img v-if="previewImage" :src="previewImage" class="w-100 h-100 object-fit-cover">
                                    <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center text-muted small"><i class="bi bi-image"></i></div>
                                </div>
                                <div class="flex-grow-1">
                                    <input @change="handleImageUpload" type="file" class="form-control form-control-sm" accept="image/*" id="fileInputBtn">
                                    <small class="text-muted d-block mt-1" style="font-size: 0.7rem">Pilih gambar, lalu potong.</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" v-if="modalType === 'service'">
                            <label class="form-label small fw-bold text-muted">WARNA AKSEN</label>
                            <div class="d-flex align-items-center gap-2">
                                <input v-model="form.color" type="color" class="form-control form-control-color" style="width: 60px; height: 38px; padding: 2px;">
                                <input v-model="form.color" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col-12 mb-3" v-if="modalType === 'service'">
                            <label class="form-label small fw-bold text-muted">LINK DETAIL SERVICE</label>
                            <input v-model="form.link" type="text" class="form-control" placeholder="/services/epc-power">
                        </div>
                    </div>

                    <div v-if="modalType === 'stat'">
                        <div class="mb-3"><label class="form-label small fw-bold text-muted">ANGKA (VALUE)</label><input v-model="form.value" type="text" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label small fw-bold text-muted">LABEL</label><input v-model="form.label" type="text" class="form-control" required></div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3"><label class="form-label small fw-bold text-muted">STATUS</label><select v-model="form.is_active" class="form-select"><option :value="1">Aktif</option><option :value="0">Non-Aktif</option></select></div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" @click="closeModal" class="btn btn-light border rounded-pill px-4">Batal</button>
                        <button type="submit" class="btn btn-navy rounded-pill px-4" :disabled="form.processing">{{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div v-if="showCropperModal" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3" style="z-index: 1060;">
        <div class="modal-content-custom bg-white rounded-4 shadow-lg w-100 overflow-hidden" style="max-width: 600px;">
            <div class="modal-header px-4 py-3 bg-navy text-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold m-0">Potong Gambar ({{ modalType === 'service' ? '9:16' : '16:9' }})</h6>
                <button @click="cancelCrop" class="btn-close btn-close-white"></button>
            </div>
            <div class="modal-body p-0 bg-dark">
                 <cropper ref="cropperRef" class="cropper-container" :src="cropperImgSrc" :stencil-props="cropStencilProps" image-class="cropper-image-contain"/>
            </div>
            <div class="modal-footer p-3 bg-white d-flex justify-content-end gap-2">
                <button @click="cancelCrop" class="btn btn-light border rounded-pill">Batal</button>
                <button @click="cropImage" class="btn btn-navy rounded-pill px-4"><i class="bi bi-crop me-2"></i> Potong & Gunakan</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Style sama seperti sebelumnya */
.cursor-move { cursor: move; }
.ghost-card { opacity: 0.5; background: #e0f2fe; border: 2px dashed #002b49; }
.nav-tabs-wrapper { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px; }
.tab-btn { border: 1px solid #e2e8f0; background: #fff; color: #64748b; padding: 10px 20px; border-radius: 50px; font-weight: 600; font-size: 0.9rem; white-space: nowrap; transition: 0.3s; }
.tab-btn.active { background-color: #002b49; color: #fff; border-color: #002b49; box-shadow: 0 4px 6px rgba(0, 43, 73, 0.2); }
.nova-card { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); transition: transform 0.2s; }
.nova-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
.bg-gradient-dark { background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); }
.text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.service-img-box { width: 80px; height: 80px; border: 3px solid transparent; padding: 2px; }
.object-fit-cover { object-fit: cover; }
.text-navy { color: #002b49; }
.bg-navy { background-color: #002b49; }
.btn-navy { background-color: #002b49; color: #fff; border:none; transition: 0.3s; }
.btn-navy:hover { background-color: #00406b; transform: translateY(-2px); }
.btn-outline-navy { color: #002b49; border: 1px solid #002b49; transition: 0.3s; }
.btn-outline-navy:hover { background-color: #002b49; color: #fff; }
.bg-success-subtle { background-color: #d1e7dd; color: #0f5132; }
.bg-danger-subtle { background-color: #f8d7da; color: #842029; }
.btn-icon { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border:none; transition: 0.2s; }
.btn-icon:hover { background-color: #e2e8f0; }
.modal-backdrop-custom { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
.modal-content-custom { animation: slideUp 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.form-label { font-size: 0.75rem; letter-spacing: 0.5px; }
.form-control, .form-select { background-color: #f8fafc; border: 1px solid #cbd5e1; }
.form-control:focus { background-color: #fff; border-color: #002b49; box-shadow: 0 0 0 3px rgba(0, 43, 73, 0.1); }
.cropper-container { height: 400px; background-color: #000; }
:deep(.cropper-image-contain) { object-fit: contain; }
.cursor-pointer { cursor: pointer; }

/* Custom Table */
.custom-table thead th { font-size: 0.8rem; font-weight: 700; color: #64748b; border-bottom: 1px solid #f1f5f9; }
.custom-table td { padding: 1rem; border-bottom: 1px solid #f8fafc; font-size: 0.9rem; }
</style>