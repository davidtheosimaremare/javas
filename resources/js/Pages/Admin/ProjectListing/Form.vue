<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import Swal from 'sweetalert2'

const props = defineProps({
    project: { type: Object, default: null }
})

const isEdit = computed(() => !!props.project)
const activeTab = ref('info')

// --- ASSET PETA ---
const mapImageSrc = 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/17/Geographical_units_of_Indonesia.svg/1200px-Geographical_units_of_Indonesia.svg.png'

// --- HELPER IMAGE ---
const getImgUrl = (path) => {
    if (!path) return '/images/no-image.png'; 
    if (path.startsWith('http') || path.startsWith('blob:')) return path;
    if (path.includes('defaults/')) return `/images/${path.replace(/^\/?storage\//, '')}`; 
    const cleanPath = path.replace(/^\/?storage\//, '');
    return `/storage/${cleanPath}?t=${new Date().getTime()}`;
}

// --- MAIN FORM ---
const form = useForm({
    _method: 'POST',
    title: props.project?.title || '',
    client: props.project?.client || '',
    year: props.project?.year || '',
    status: props.project?.status || 'Completed',
    category: props.project?.category || '',
    location: props.project?.location || '',
    scope: props.project?.scope || '',
    
    overview: props.project?.overview || '',
    challenge: props.project?.challenge || '',
    solution: props.project?.solution || '',
    
    map_area: props.project?.map_area || '',
    // Default koordinat (Jakarta) jika kosong
    coord_top: props.project?.coord_top || '65%', 
    coord_left: props.project?.coord_left || '30%',
    
    is_active: props.project?.is_active ?? 1,
    hero_image: null,
    new_gallery_images: [] 
})

const previewHero = ref(props.project?.hero_image ? getImgUrl(props.project.hero_image) : null)

// --- LOGIC DRAG N DROP MAP ---
const mapContainerRef = ref(null)
const isDragging = ref(false)

const startDrag = (e) => {
    e.preventDefault() // Mencegah seleksi gambar
    isDragging.value = true
    
    // Tambahkan listener ke window agar drag lancar meski keluar area kecil
    window.addEventListener('mousemove', onDrag)
    window.addEventListener('mouseup', stopDrag)
}

const onDrag = (e) => {
    if (!isDragging.value || !mapContainerRef.value) return
    calculatePosition(e.clientX, e.clientY)
}

const stopDrag = () => {
    isDragging.value = false
    window.removeEventListener('mousemove', onDrag)
    window.removeEventListener('mouseup', stopDrag)
}

// Handle klik langsung pada peta (pindah pin instan)
const handleMapClick = (e) => {
    calculatePosition(e.clientX, e.clientY)
}

const calculatePosition = (clientX, clientY) => {
    const rect = mapContainerRef.value.getBoundingClientRect()
    
    // Hitung posisi relatif mouse terhadap container peta
    let x = clientX - rect.left
    let y = clientY - rect.top

    // Batasi agar tidak keluar kotak
    x = Math.max(0, Math.min(x, rect.width))
    y = Math.max(0, Math.min(y, rect.height))

    // Konversi ke Persentase
    const percentX = (x / rect.width) * 100
    const percentY = (y / rect.height) * 100

    // Simpan ke form (maksimal 2 desimal)
    form.coord_left = percentX.toFixed(2) + '%'
    form.coord_top = percentY.toFixed(2) + '%'
}

// --- ACTIONS UTAMA ---
const submit = () => {
    const url = isEdit.value ? `/admin/projects/${props.project.id}` : '/admin/projects'
    
    form.post(url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Data proyek berhasil disimpan!', timer: 2000, showConfirmButton: false })
        },
        onError: () => {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Periksa kembali inputan Anda.' })
        }
    })
}

const handleHeroUpload = (e) => {
    const file = e.target.files[0];
    if(file) { 
        form.hero_image = file; 
        previewHero.value = URL.createObjectURL(file); 
    }
}

// --- GALLERY & TESTI ---
const handleGalleryUpload = (e) => { form.new_gallery_images = Array.from(e.target.files); }
const deleteGalleryItem = (id) => {
    Swal.fire({ title: 'Hapus foto ini?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Hapus!' })
    .then((result) => { if (result.isConfirmed) { router.delete(`/admin/projects/gallery/${id}`, { preserveScroll: true, onSuccess: () => Swal.fire('Terhapus!', '', 'success') }) } })
}

const isTestiModalOpen = ref(false)
const formTesti = useForm({ name: '', position: '', quote: '', avatar: null })
const previewAvatar = ref(null)
const handleAvatarUpload = (e) => { const file = e.target.files[0]; if(file) { formTesti.avatar = file; previewAvatar.value = URL.createObjectURL(file); } }
const submitTesti = () => {
    formTesti.post(`/admin/projects/${props.project.id}/testimonial`, {
        onSuccess: () => { isTestiModalOpen.value = false; formTesti.reset(); previewAvatar.value = null; Swal.fire('Berhasil', 'Testimoni ditambahkan', 'success') }
    })
}
const deleteTesti = (id) => { if(confirm('Hapus testimoni ini?')) { router.delete(`/admin/projects/testimonial/${id}`, { preserveScroll: true }) } }

const quillToolbar = [['bold', 'italic', 'underline'], [{ 'list': 'ordered'}, { 'list': 'bullet' }], ['clean']];
</script>

<template>
    <Head :title="isEdit ? 'Edit Proyek' : 'Tambah Proyek'" />

    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">{{ isEdit ? 'Edit Proyek' : 'Buat Proyek Baru' }}</h3>
                <p class="text-muted mb-0">Kelola detail proyek, galeri, dan testimoni.</p>
            </div>
            <button @click="submit" class="btn btn-navy rounded-pill px-4" :disabled="form.processing">
                <i class="bi bi-check-lg me-2"></i> Simpan Proyek
            </button>
        </div>

        <div class="nav-tabs-wrapper mb-4">
            <button class="tab-btn" :class="{ active: activeTab === 'info' }" @click="activeTab = 'info'"><i class="bi bi-info-circle me-2"></i> Informasi Utama</button>
            <button class="tab-btn" :class="{ active: activeTab === 'detail' }" @click="activeTab = 'detail'"><i class="bi bi-file-text me-2"></i> Detail & Cerita</button>
            <button class="tab-btn" :class="{ active: activeTab === 'map' }" @click="activeTab = 'map'"><i class="bi bi-map me-2"></i> Lokasi Peta</button>
            <button class="tab-btn" :class="{ active: activeTab === 'gallery' }" @click="activeTab = 'gallery'" :disabled="!isEdit"><i class="bi bi-images me-2"></i> Galeri Foto</button>
            <button class="tab-btn" :class="{ active: activeTab === 'testi' }" @click="activeTab = 'testi'" :disabled="!isEdit"><i class="bi bi-chat-quote me-2"></i> Testimoni</button>
        </div>

        <div class="nova-card p-4">
            <form @submit.prevent="submit">
                
                <div v-show="activeTab === 'info'" class="row g-3">
                    <div class="col-md-8"><label class="form-label fw-bold">Judul Proyek</label><input v-model="form.title" type="text" class="form-control" required placeholder="Nama Proyek..."></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Status</label><select v-model="form.status" class="form-select"><option value="Completed">Completed (Selesai)</option><option value="On Progress">On Progress (Berjalan)</option></select></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Klien</label><input v-model="form.client" type="text" class="form-control" placeholder="Nama PT/Instansi"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Tahun</label><input v-model="form.year" type="text" class="form-control" placeholder="2024"></div>
                    <div class="col-md-4"><label class="form-label fw-bold">Kategori</label><input v-model="form.category" type="text" class="form-control" placeholder="Ex: Power Generation"></div>
                    <div class="col-12 mt-3">
                        <label class="form-label fw-bold">Hero Image (Cover)</label>
                        <div class="d-flex align-items-center gap-3 border rounded p-3 bg-light">
                            <div class="preview-box border rounded overflow-hidden bg-secondary" style="width: 120px; height: 70px;">
                                <img v-if="previewHero" :src="previewHero" class="w-100 h-100 object-fit-cover">
                                <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center text-white-50 small">No Img</div>
                            </div>
                            <input type="file" class="form-control w-auto" @change="handleHeroUpload" accept="image/*">
                        </div>
                    </div>
                    <div class="col-md-4 mt-3"><label class="form-label fw-bold">Visibilitas</label><select v-model="form.is_active" class="form-select"><option :value="1">Tampilkan (Public)</option><option :value="0">Sembunyikan (Draft)</option></select></div>
                </div>

                <div v-show="activeTab === 'detail'" class="row g-4">
                    <div class="col-12">
                        <label class="form-label fw-bold">Overview (Ringkasan)</label>
                        <div class="quill-wrapper border rounded"><QuillEditor theme="snow" v-model:content="form.overview" contentType="html" :toolbar="quillToolbar" /></div>
                    </div>
                    <div class="col-12"><label class="form-label fw-bold">Lingkup Kerja (Scope)</label><input v-model="form.scope" type="text" class="form-control" placeholder="Ex: EPC, Procurement, Installation"></div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Challenge (Tantangan)</label>
                        <div class="quill-wrapper border rounded"><QuillEditor theme="snow" v-model:content="form.challenge" contentType="html" :toolbar="quillToolbar" /></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Solution (Solusi)</label>
                        <div class="quill-wrapper border rounded"><QuillEditor theme="snow" v-model:content="form.solution" contentType="html" :toolbar="quillToolbar" /></div>
                    </div>
                </div>

                <div v-show="activeTab === 'map'" class="row g-4">
                    <div class="col-12">
                        <div class="alert alert-info d-flex align-items-center mb-0">
                            <i class="bi bi-info-circle-fill fs-5 me-3"></i>
                            <div>
                                <strong>Mode Drag & Drop:</strong> Klik pada peta atau seret pin merah untuk menentukan lokasi proyek. Koordinat akan otomatis terisi.
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="map-editor-container border rounded position-relative overflow-hidden" 
                             ref="mapContainerRef"
                             @mousedown="handleMapClick">
                            
                            <img :src="mapImageSrc" alt="Peta Indonesia" class="w-100 h-100 object-fit-contain map-bg">
                            
                            <div 
                                class="map-pin"
                                :style="{ top: form.coord_top, left: form.coord_left }"
                                @mousedown.stop="startDrag"
                            >
                                <div class="pin-marker"></div>
                                <div class="pin-pulse"></div>
                            </div>

                        </div>
                        <small class="text-muted mt-2 d-block text-center">Peta Indonesia (Klik untuk memindahkan pin)</small>
                    </div>

                    <div class="col-lg-4">
                        <div class="card bg-light border-0 p-3 h-100">
                            <h6 class="fw-bold text-navy mb-3">Detail Lokasi</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">NAMA AREA (PULAU/KOTA)</label>
                                <input v-model="form.map_area" type="text" class="form-control" placeholder="Ex: Jawa Timur">
                            </div>

                            <hr class="border-secondary opacity-10">

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">KOORDINAT TOP (%)</label>
                                <input v-model="form.coord_top" type="text" class="form-control bg-white font-monospace" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">KOORDINAT LEFT (%)</label>
                                <input v-model="form.coord_left" type="text" class="form-control bg-white font-monospace" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-show="activeTab === 'gallery'" class="row g-3">
                    <div v-if="!isEdit" class="text-center text-muted py-5">Simpan proyek terlebih dahulu untuk menambahkan galeri.</div>
                    <div v-else>
                        <div class="mb-4 p-3 bg-blue-subtle rounded border-0">
                            <label class="form-label fw-bold text-navy">Upload Foto Baru</label>
                            <div class="d-flex gap-3">
                                <input type="file" class="form-control" multiple @change="handleGalleryUpload" accept="image/*">
                                <small class="text-muted align-self-center">Multiple files supported.</small>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3 col-6" v-for="gal in project.galleries" :key="gal.id">
                                <div class="position-relative rounded overflow-hidden shadow-sm group-hover-container" style="height: 140px;">
                                    <img :src="gal.image_path" class="w-100 h-100 object-fit-cover">
                                    <div class="overlay-delete d-flex align-items-center justify-content-center">
                                        <button type="button" @click="deleteGalleryItem(gal.id)" class="btn btn-danger btn-sm rounded-circle"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-show="activeTab === 'testi'">
                    <div v-if="!isEdit" class="text-center text-muted py-5">Simpan proyek terlebih dahulu untuk menambahkan testimoni.</div>
                    <div v-else>
                        <div class="mb-3 text-end">
                            <button type="button" @click="isTestiModalOpen = true" class="btn btn-outline-navy btn-sm rounded-pill"><i class="bi bi-plus-lg"></i> Tambah Testimoni</button>
                        </div>
                        <div class="list-group shadow-sm">
                            <div v-for="testi in project.testimonials" :key="testi.id" class="list-group-item d-flex align-items-center justify-content-between p-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img :src="testi.avatar || '/images/no-image.png'" class="rounded-circle border" style="width: 45px; height: 45px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ testi.name }}</div>
                                        <small class="text-muted">{{ testi.position }}</small>
                                    </div>
                                </div>
                                <button type="button" @click="deleteTesti(testi.id)" class="btn btn-sm text-danger bg-light rounded-circle" style="width:32px;height:32px"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </AdminLayout>

    <div v-if="isTestiModalOpen" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3">
        <div class="modal-content-custom bg-white rounded-4 shadow-lg w-100" style="max-width: 500px;">
            <div class="modal-header px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0 text-navy">Tambah Testimoni</h5>
                <button @click="isTestiModalOpen = false" class="btn-close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label small fw-bold text-muted">NAMA</label><input v-model="formTesti.name" class="form-control" required></div>
                <div class="mb-3"><label class="form-label small fw-bold text-muted">JABATAN</label><input v-model="formTesti.position" class="form-control" required></div>
                <div class="mb-3"><label class="form-label small fw-bold text-muted">TESTIMONI</label><textarea v-model="formTesti.quote" class="form-control" rows="3" required></textarea></div>
                <div class="mb-3"><label class="form-label small fw-bold text-muted">AVATAR</label><input @change="handleAvatarUpload" type="file" class="form-control"></div>
                <div class="text-end"><button @click="submitTesti" class="btn btn-navy rounded-pill px-4" :disabled="formTesti.processing">Simpan</button></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* MAP EDITOR STYLE */
.map-editor-container {
    position: relative;
    width: 100%;
    background-color: #e9ecef;
    cursor: crosshair; /* Cursor target */
}
.map-bg {
    pointer-events: none; /* Biar klik tembus ke container */
    opacity: 0.6;
    filter: grayscale(100%) contrast(1.2);
}
.map-pin {
    position: absolute;
    transform: translate(-50%, -50%);
    cursor: grab;
    z-index: 10;
}
.map-pin:active { cursor: grabbing; }
.pin-marker {
    width: 16px; height: 16px;
    background-color: #e63946; /* Merah */
    border: 2px solid white;
    border-radius: 50%;
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
}
.pin-pulse {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
    width: 30px; height: 30px;
    background-color: rgba(230, 57, 70, 0.3);
    border-radius: 50%;
    animation: pulse 2s infinite;
    pointer-events: none;
}
@keyframes pulse { 0% { transform: translate(-50%, -50%) scale(0.5); opacity: 1; } 100% { transform: translate(-50%, -50%) scale(1.5); opacity: 0; } }

/* Common Styles */
.nav-tabs-wrapper { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px; }
.tab-btn { border: 1px solid #e2e8f0; background: #fff; color: #64748b; padding: 8px 20px; border-radius: 50px; font-weight: 600; font-size: 0.9rem; transition: 0.3s; }
.tab-btn.active { background-color: #002b49; color: #fff; border-color: #002b49; }
.tab-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.nova-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
.text-navy { color: #002b49; }
.btn-navy { background-color: #002b49; color: #fff; border: none; }
.btn-navy:hover { background-color: #00406b; }
.btn-outline-navy { border: 1px solid #002b49; color: #002b49; }
.btn-outline-navy:hover { background-color: #002b49; color: #fff; }
.bg-blue-subtle { background-color: #e0f2fe; }
.object-fit-cover { object-fit: cover; }
.form-control:focus { border-color: #002b49; box-shadow: 0 0 0 3px rgba(0, 43, 73, 0.1); }
.modal-backdrop-custom { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1050; }
.modal-content-custom { animation: slideUp 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.quill-wrapper { background-color: #fff; }
:deep(.ql-editor) { min-height: 150px; }
:deep(.ql-toolbar) { border-radius: 8px 8px 0 0 !important; background-color: #f8f9fa; border-color: #dee2e6 !important; }
:deep(.ql-container) { border-radius: 0 0 8px 8px !important; border-color: #dee2e6 !important; }
.group-hover-container .overlay-delete { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); opacity: 0; transition: 0.2s; }
.group-hover-container:hover .overlay-delete { opacity: 1; }
</style>