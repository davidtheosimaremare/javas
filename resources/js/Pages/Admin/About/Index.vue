<script setup>
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

// --- IMPORT SWEETALERT ---
import Swal from 'sweetalert2'

const props = defineProps({
    pageSetting: Object,
    aboutContent: Object,
    misiList: Array,
    leadershipTeam: Array,
    galleryImages: Array
})

// --- STATE ---
const activeTab = ref('settings')
const isModalOpen = ref(false)
const modalType = ref('') 
const editingItem = ref(null)
const previewImage = ref(null)

// --- HELPERS ---
function getImgUrl(path) {
    if (!path) return '/images/no-image.png'; 
    if (path.startsWith('http') || path.startsWith('blob:')) return path;
    if (path.includes('defaults/')) return `/images/${path.replace(/^\/?storage\//, '')}`; 
    const cleanPath = path.replace(/^\/?storage\//, '');
    return `/storage/${cleanPath}?t=${new Date().getTime()}`;
}

// --- SWEETALERT ---
const showSuccessToast = (message) => {
    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: message, showConfirmButton: false, timer: 3000, timerProgressBar: true })
}

const showConfirmDelete = (callback) => {
    Swal.fire({ title: 'Hapus item ini?', text: "Data tidak bisa dikembalikan!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, Hapus!' }).then((result) => { if (result.isConfirmed) callback() })
}

// --- FORMS ---

// 1. Form Hero
const formSettings = useForm({
    _method: 'POST',
    hero_title: props.pageSetting?.hero_title || 'Tentang Kami',
    hero_bg_path: null, 
})
const previewHeroBg = ref(props.pageSetting?.hero_bg_path ? getImgUrl(props.pageSetting.hero_bg_path) : null)

// 2. Form Konten
const formContent = useForm({
    page_title: props.aboutContent?.page_title || '',
    quote: props.aboutContent?.quote || '',
    content_html: props.aboutContent?.content_html || '',
})

// 3. Form Quote
const formQuote = useForm({
    section_subtitle: props.aboutContent?.section_subtitle || '',
    quote: props.aboutContent?.quote || '',
    quote_explanation_text: props.aboutContent?.quote_explanation_text || '',
})

// 4. Form Modal (Universal: Misi, Team, Gallery)
const formModal = useForm({
    _method: 'POST',
    type: 'misi', content: '', // Misi
    name: '', title: '', // Team
    image: null, caption: '' // Gallery (Image & Caption)
})

// --- HANDLERS ---

const submitSettings = () => {
    formSettings.post('/admin/about-editor/page-setting', { preserveScroll: true, forceFormData: true, onSuccess: () => showSuccessToast('Settings disimpan!') })
}
const handleHeroUpload = (e) => { const file = e.target.files[0]; if(file) { formSettings.hero_bg_path = file; previewHeroBg.value = URL.createObjectURL(file); } }

const submitContent = () => {
    formContent.post('/admin/about-editor/content', { preserveScroll: true, onSuccess: () => showSuccessToast('Konten disimpan!') })
}

const submitQuote = () => {
    formQuote.post('/admin/about-editor/quote', { preserveScroll: true, onSuccess: () => showSuccessToast('Quote disimpan!') })
}

// --- MODAL LOGIC ---
const openModal = (type, item = null) => {
    modalType.value = type
    editingItem.value = item
    formModal.clearErrors(); formModal.reset(); previewImage.value = null
    
    if (item) {
        // Edit Mode
        if(type === 'misi') { formModal.type = item.type; formModal.content = item.content }
        if(type === 'team') { formModal.name = item.name; formModal.title = item.title; if (item.image_url) previewImage.value = getImgUrl(item.image_url) }
        // Gallery biasanya hapus & add, tapi kalau mau edit caption bisa ditambah
    } else {
        // Create Mode Defaults
        if (type === 'misi') formModal.type = 'misi'
    }
    isModalOpen.value = true
}

const closeModal = () => { isModalOpen.value = false; editingItem.value = null }
const handleModalImageUpload = (e) => { const file = e.target.files[0]; if(file) { formModal.image = file; previewImage.value = URL.createObjectURL(file); } }

const submitModal = () => {
    let url = ''
    if (modalType.value === 'misi') url = editingItem.value ? `/admin/about-editor/misi/${editingItem.value.id}` : '/admin/about-editor/misi'
    else if (modalType.value === 'team') url = editingItem.value ? `/admin/about-editor/leader/${editingItem.value.id}` : '/admin/about-editor/leader'
    else if (modalType.value === 'gallery') url = '/admin/about-editor/gallery' // Gallery Create Only

    formModal.post(url, { 
        preserveScroll: true, forceFormData: true, 
        onSuccess: () => { closeModal(); showSuccessToast('Data berhasil disimpan!') } 
    })
}

const deleteItem = (type, id) => {
    showConfirmDelete(() => {
        let url = ''
        if(type === 'misi') url = `/admin/about-editor/misi/${id}`
        if(type === 'team') url = `/admin/about-editor/leader/${id}`
        if(type === 'gallery') url = `/admin/about-editor/gallery/${id}`
        router.delete(url, { preserveScroll: true, onSuccess: () => showSuccessToast('Item dihapus.') })
    })
}
</script>

<template>
    <Head title="Manajemen About Us" />

    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-slate-800 mb-0">Manajemen About Us</h3>
        </div>

        <div class="nav-tabs-wrapper mb-4">
            <button class="tab-btn" :class="{ active: activeTab === 'settings' }" @click="activeTab = 'settings'"><i class="bi bi-sliders me-2"></i> Hero & Settings</button>
            <button class="tab-btn" :class="{ active: activeTab === 'content' }" @click="activeTab = 'content'"><i class="bi bi-file-text me-2"></i> Konten Utama</button>
            <button class="tab-btn" :class="{ active: activeTab === 'quote' }" @click="activeTab = 'quote'"><i class="bi bi-chat-quote me-2"></i> Quote</button>
            <button class="tab-btn" :class="{ active: activeTab === 'misi' }" @click="activeTab = 'misi'"><i class="bi bi-bullseye me-2"></i> Visi & Misi</button>
            <button class="tab-btn" :class="{ active: activeTab === 'team' }" @click="activeTab = 'team'"><i class="bi bi-people me-2"></i> Leadership</button>
            <button class="tab-btn" :class="{ active: activeTab === 'gallery' }" @click="activeTab = 'gallery'"><i class="bi bi-images me-2"></i> Galeri</button>
        </div>

        <div v-if="activeTab === 'settings'">
            <div class="nova-card p-4 border-start border-5 border-navy">
                <h6 class="fw-bold text-navy mb-3">Pengaturan Hero Page</h6>
                <form @submit.prevent="submitSettings">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">HERO TITLE</label>
                            <input v-model="formSettings.hero_title" type="text" class="form-control form-control-lg fw-bold" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">BACKGROUND BANNER (16:9)</label>
                            <div class="d-flex align-items-center gap-3 border rounded p-3 bg-light">
                                <div class="preview-box border rounded overflow-hidden bg-secondary" style="width: 160px; height: 90px;">
                                    <img v-if="previewHeroBg" :src="previewHeroBg" class="w-100 h-100 object-fit-cover">
                                    <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center text-white-50 small">No Img</div>
                                </div>
                                <div class="flex-grow-1">
                                    <input @change="handleHeroUpload" type="file" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-navy rounded-pill px-5" :disabled="formSettings.processing">Simpan Settings</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="activeTab === 'content'">
            <div class="nova-card p-4">
                <h6 class="fw-bold text-navy mb-3">Konten Halaman (Body)</h6>
                <form @submit.prevent="submitContent">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">PAGE TITLE (JUDUL SEKSI)</label>
                        <input v-model="formContent.page_title" type="text" class="form-control form-control-lg">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">ISI KONTEN LENGKAP</label>
                        <div class="quill-wrapper border rounded">
                            <QuillEditor theme="snow" v-model:content="formContent.content_html" contentType="html" toolbar="essential" />
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-navy rounded-pill px-4" :disabled="formContent.processing">Simpan Konten</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="activeTab === 'quote'">
            <div class="nova-card p-4 border-start border-5 border-danger">
                <h6 class="fw-bold text-navy mb-3">Quote & Keterangan Tambahan</h6>
                <form @submit.prevent="submitQuote">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">SUBTITLE</label>
                        <input v-model="formQuote.section_subtitle" type="text" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">MAIN QUOTE</label>
                        <textarea v-model="formQuote.quote" class="form-control fst-italic fs-5 text-center" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">PENJELASAN QUOTE</label>
                        <textarea v-model="formQuote.quote_explanation_text" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-navy rounded-pill px-4" :disabled="formQuote.processing">Simpan Quote</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="activeTab === 'misi'">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-navy m-0">Daftar Visi & Misi</h6>
                <button @click="openModal('misi')" class="btn btn-sm btn-outline-navy rounded-pill"><i class="bi bi-plus-lg"></i> Tambah Item</button>
            </div>
            <div class="row g-3">
                <div class="col-12" v-for="item in misiList" :key="item.id">
                    <div class="nova-card p-3 d-flex align-items-start justify-content-between">
                        <div>
                            <span class="badge mb-2" :class="item.type === 'visi' ? 'bg-primary' : 'bg-success'">{{ item.type.toUpperCase() }}</span>
                            <p class="mb-0 text-dark">{{ item.content }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button @click="openModal('misi', item)" class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                            <button @click="deleteItem('misi', item.id)" class="btn btn-sm btn-light border text-danger"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'team'">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-navy m-0">Tim Leadership</h6>
                <button @click="openModal('team')" class="btn btn-sm btn-outline-navy rounded-pill"><i class="bi bi-plus-lg"></i> Tambah Tim</button>
            </div>
            <div class="row g-4">
                <div class="col-md-4 col-lg-3" v-for="person in leadershipTeam" :key="person.id">
                    <div class="nova-card text-center h-100">
                        <div class="mx-auto mb-3 rounded-circle overflow-hidden shadow-sm" style="width: 100px; height: 100px;">
                            <img :src="getImgUrl(person.image_url)" class="w-100 h-100 object-fit-cover">
                        </div>
                        <h6 class="fw-bold text-dark mb-1">{{ person.name }}</h6>
                        <p class="text-muted small mb-3">{{ person.title }}</p>
                        <div class="d-flex justify-content-center gap-2 border-top pt-3">
                            <button @click="openModal('team', person)" class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                            <button @click="deleteItem('team', person.id)" class="btn btn-sm btn-light border text-danger"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'gallery'">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-navy m-0">Galeri Foto</h6>
                <button @click="openModal('gallery')" class="btn btn-sm btn-outline-navy rounded-pill"><i class="bi bi-plus-lg"></i> Tambah Foto</button>
            </div>

            <div class="row g-3">
                <div class="col-6 col-md-3" v-for="img in galleryImages" :key="img.id">
                    <div class="position-relative rounded overflow-hidden shadow-sm group-hover-container gallery-card">
                        <img :src="getImgUrl(img.image_url)" class="w-100 h-100 object-fit-cover">
                        <div class="caption-overlay">
                            <p class="m-0 text-white small text-truncate">{{ img.caption || 'Tanpa Caption' }}</p>
                        </div>
                        <div class="overlay-delete d-flex align-items-center justify-content-center">
                            <button @click="deleteItem('gallery', img.id)" class="btn btn-danger btn-sm rounded-circle shadow">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AdminLayout>

    <div v-if="isModalOpen" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3">
        <div class="modal-content-custom bg-white rounded-4 shadow-lg w-100" style="max-width: 500px;">
            <div class="modal-header px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0 text-navy">
                    {{ editingItem ? 'Edit' : 'Tambah' }} 
                    <span v-if="modalType === 'misi'">Visi / Misi</span>
                    <span v-if="modalType === 'team'">Anggota Tim</span>
                    <span v-if="modalType === 'gallery'">Foto Galeri</span>
                </h5>
                <button @click="closeModal" class="btn-close"></button>
            </div>
            <div class="modal-body p-4">
                <form @submit.prevent="submitModal">
                    
                    <div v-if="modalType === 'misi'">
                        <div class="mb-3"><label class="form-label small fw-bold text-muted">TIPE</label><select v-model="formModal.type" class="form-select"><option value="visi">Visi</option><option value="misi">Misi</option></select></div>
                        <div class="mb-3"><label class="form-label small fw-bold text-muted">ISI KONTEN</label><textarea v-model="formModal.content" class="form-control" rows="3" required></textarea></div>
                    </div>

                    <div v-if="modalType === 'team'">
                        <div class="mb-3"><label class="form-label small fw-bold text-muted">NAMA LENGKAP</label><input v-model="formModal.name" type="text" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label small fw-bold text-muted">JABATAN</label><input v-model="formModal.title" type="text" class="form-control" required></div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">FOTO PROFIL (1:1)</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="preview-box border rounded-circle overflow-hidden bg-light" style="width: 60px; height: 60px;"><img v-if="previewImage" :src="previewImage" class="w-100 h-100 object-fit-cover"><div v-else class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-person"></i></div></div>
                                <input @change="handleModalImageUpload" type="file" class="form-control form-control-sm" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div v-if="modalType === 'gallery'">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">CAPTION (KETERANGAN)</label>
                            <input v-model="formModal.caption" type="text" class="form-control" placeholder="Contoh: Kegiatan Briefing Pagi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">UPLOAD FOTO</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="preview-box border rounded overflow-hidden bg-light" style="width: 80px; height: 60px;">
                                    <img v-if="previewImage" :src="previewImage" class="w-100 h-100 object-fit-cover">
                                    <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
                                </div>
                                <input @change="handleModalImageUpload" type="file" class="form-control form-control-sm" accept="image/*" required>
                            </div>
                        </div>
                    </div>

                    <div class="text-end pt-3">
                        <button type="submit" class="btn btn-navy rounded-pill px-4" :disabled="formModal.processing">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* ... Style yang sama seperti sebelumnya ... */
.nav-tabs-wrapper { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px; }
.tab-btn { border: 1px solid #e2e8f0; background: #fff; color: #64748b; padding: 10px 20px; border-radius: 50px; font-weight: 600; font-size: 0.9rem; white-space: nowrap; transition: 0.3s; }
.tab-btn.active { background-color: #002b49; color: #fff; border-color: #002b49; box-shadow: 0 4px 6px rgba(0, 43, 73, 0.2); }
.nova-card { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); transition: transform 0.2s; }
.text-navy { color: #002b49; }
.btn-navy { background-color: #002b49; color: #fff; border:none; transition: 0.3s; }
.btn-navy:hover { background-color: #00406b; }
.btn-outline-navy { border: 1px solid #002b49; color: #002b49; transition: 0.3s; }
.btn-outline-navy:hover { background-color: #002b49; color: #fff; }
.bg-blue-subtle { background-color: #e0f2fe; }
.modal-backdrop-custom { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1050; }
.modal-content-custom { animation: slideUp 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.object-fit-cover { object-fit: cover; }
.form-control:focus { border-color: #002b49; box-shadow: 0 0 0 3px rgba(0, 43, 73, 0.1); }
.quill-wrapper { background-color: #fff; }
:deep(.ql-editor) { min-height: 300px; font-size: 1rem; line-height: 1.6; }
:deep(.ql-toolbar) { border-radius: 8px 8px 0 0 !important; background-color: #f8f9fa; border-color: #dee2e6 !important; }
:deep(.ql-container) { border-radius: 0 0 8px 8px !important; border-color: #dee2e6 !important; }

/* Gallery Styling */
.gallery-card { height: 180px; }
.caption-overlay { position: absolute; bottom: 0; left: 0; width: 100%; background: rgba(0,0,0,0.7); padding: 8px 12px; transition: 0.3s; }
.group-hover-container .overlay-delete { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.2s; z-index: 2; }
.group-hover-container:hover .overlay-delete { opacity: 1; }
</style>