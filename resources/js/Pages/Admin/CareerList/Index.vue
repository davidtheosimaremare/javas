<script setup>
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    jobs: Array // Hanya butuh data jobs sekarang
})

// --- STATE ---
const isModalOpen = ref(false)
const editingItem = ref(null)

// --- FORM ---
const form = useForm({
    _method: 'POST',
    title: '', 
    department: '', 
    type: 'Full Time', 
    location: '', 
    experience: '', 
    description: '', 
    is_active: 1
})

// --- ACTIONS ---

// Buka Modal (Tambah / Edit)
const openModal = (item = null) => {
    editingItem.value = item
    form.clearErrors()
    form.reset()
    
    if (item) {
        // Edit Mode
        form.title = item.title || ''
        form.department = item.department || ''
        form.type = item.type || 'Full Time'
        form.location = item.location || ''
        form.experience = item.experience || ''
        form.description = item.description || ''
        form.is_active = item.is_active ? 1 : 0
    } else {
        // Create Mode Defaults
        form.type = 'Full Time'
        form.is_active = 1
    }
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    editingItem.value = null
}

const submitModal = () => {
    const url = editingItem.value 
        ? `/admin/career-list/job/${editingItem.value.id}` 
        : '/admin/career-list/job'

    form.post(url, { 
        preserveScroll: true, 
        onSuccess: () => closeModal() 
    })
}

const deleteItem = (id) => {
    if(!confirm('Hapus lowongan ini? Data pelamar mungkin masih terkait.')) return;
    router.delete(`/admin/career-list/job/${id}`, { preserveScroll: true })
}
</script>

<template>
    <Head title="Manajemen Lowongan Kerja" />

    <AdminLayout>
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">Manajemen Lowongan Kerja</h3>
                <p class="text-muted mb-0">Kelola daftar posisi yang terbuka untuk pelamar.</p>
            </div>
            
            <button @click="openModal()" class="btn btn-navy rounded-pill px-4 mt-3 mt-md-0">
                <i class="bi bi-plus-lg me-2"></i> Tambah Lowongan
            </button>
        </div>

        <div class="nova-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 custom-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">POSISI</th>
                            <th>DEPARTEMEN</th>
                            <th>LOKASI</th>
                            <th>TIPE</th>
                            <th>STATUS</th>
                            <th class="text-end pe-4">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="jobs.length === 0">
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-briefcase fs-1 d-block mb-2 opacity-50"></i>
                                Belum ada lowongan kerja aktif.
                            </td>
                        </tr>
                        <tr v-for="job in jobs" :key="job.id">
                            <td class="ps-4 fw-bold text-dark">{{ job.title }}</td>
                            <td class="text-muted small">
                                <span class="d-flex align-items-center gap-2">
                                    <i class="bi bi-building text-secondary"></i> {{ job.department }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                <i class="bi bi-geo-alt me-1 text-secondary"></i> {{ job.location }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ job.type }}</span>
                            </td>
                            <td>
                                <span class="badge rounded-pill" 
                                    :class="job.is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'">
                                    {{ job.is_active ? 'Active' : 'Closed' }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button @click="openModal(job)" class="btn btn-sm btn-light border me-2" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button @click="deleteItem(job.id)" class="btn btn-sm btn-light border text-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </AdminLayout>

    <div v-if="isModalOpen" class="modal-backdrop-custom d-flex align-items-center justify-content-center p-3">
        <div class="modal-content-custom bg-white rounded-4 shadow-lg w-100" style="max-width: 600px;">
            
            <div class="modal-header px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0 text-navy">
                    {{ editingItem ? 'Edit Lowongan' : 'Tambah Lowongan Baru' }}
                </h5>
                <button @click="closeModal" class="btn-close"></button>
            </div>

            <div class="modal-body p-4">
                <form @submit.prevent="submitModal">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">JUDUL POSISI</label>
                        <input v-model="form.title" type="text" class="form-control form-control-lg" placeholder="Contoh: Electrical Engineer" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">DEPARTEMEN</label>
                            <input v-model="form.department" type="text" class="form-control" placeholder="Ex: Engineering">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">TIPE PEKERJAAN</label>
                            <select v-model="form.type" class="form-select">
                                <option>Full Time</option>
                                <option>Contract</option>
                                <option>Internship</option>
                                <option>Freelance</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">LOKASI KERJA</label>
                            <input v-model="form.location" type="text" class="form-control" placeholder="Ex: Cikarang (Site)">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold text-muted">PENGALAMAN (TAHUN)</label>
                            <input v-model="form.experience" type="text" class="form-control" placeholder="Ex: Min. 3 Tahun">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">DESKRIPSI PEKERJAAN</label>
                        <textarea v-model="form.description" class="form-control" rows="5" required placeholder="Tuliskan tanggung jawab dan kualifikasi..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">STATUS PUBLIKASI</label>
                        <select v-model="form.is_active" class="form-select">
                            <option :value="1">Active (Ditampilkan di Web)</option>
                            <option :value="0">Closed (Disembunyikan)</option>
                        </select>
                    </div>

                    <div class="text-end pt-3 border-top">
                        <button type="button" @click="closeModal" class="btn btn-light border rounded-pill me-2">Batal</button>
                        <button type="submit" class="btn btn-navy rounded-pill px-4" :disabled="form.processing">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Data' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* NOVA STYLE COLORS */
.text-navy { color: #002b49; }
.btn-navy { background-color: #002b49; color: #fff; border: none; transition: 0.3s; }
.btn-navy:hover { background-color: #00406b; transform: translateY(-2px); }

/* CARD & TABLE */
.nova-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
.custom-table thead th { font-size: 0.75rem; font-weight: 700; color: #64748b; border-bottom: 1px solid #f1f5f9; padding: 1rem; text-transform: uppercase; letter-spacing: 0.5px; }
.custom-table td { padding: 1rem; border-bottom: 1px solid #f8fafc; font-size: 0.9rem; }

/* BADGES */
.bg-success-subtle { background-color: #d1e7dd; color: #0f5132; }
.bg-secondary-subtle { background-color: #e2e3e5; color: #41464b; }

/* FORM */
.form-label { font-size: 0.75rem; letter-spacing: 0.5px; }
.form-control, .form-select { background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 10px 15px; }
.form-control:focus { border-color: #002b49; box-shadow: 0 0 0 3px rgba(0, 43, 73, 0.1); background-color: #fff; }

/* MODAL */
.modal-backdrop-custom { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); z-index: 1050; }
.modal-content-custom { animation: slideUp 0.3s ease-out; }
@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>