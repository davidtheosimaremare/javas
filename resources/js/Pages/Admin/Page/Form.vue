<script setup>
import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import Swal from 'sweetalert2'

const props = defineProps({
    page: { type: Object, default: null }
})

const isEdit = computed(() => !!props.page)

const form = useForm({
    _method: 'POST',
    title: props.page?.title || '',
    content: props.page?.content || '',
    meta_title: props.page?.meta_title || '',
    meta_description: props.page?.meta_description || '',
    is_active: props.page?.is_active ?? 1,
    hero_image: null,
})

const previewHero = ref(props.page?.hero_image ? getImgUrl(props.page.hero_image) : null)

function getImgUrl(path) {
    if (!path) return null;
    if (path.startsWith('http')) return path;
    return path.startsWith('/storage') ? path : `/storage/${path.replace(/^storage\//, '')}`;
}

const handleHeroUpload = (e) => {
    const file = e.target.files[0];
    if(file) { 
        form.hero_image = file; 
        previewHero.value = URL.createObjectURL(file); 
    }
}

const submit = () => {
    const url = isEdit.value ? `/admin/pages/${props.page.id}` : '/admin/pages'
    
    form.post(url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Halaman berhasil disimpan!', timer: 2000, showConfirmButton: false })
        },
        onError: () => {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Periksa kembali isian Anda.' })
        }
    })
}

// Tambahkan opsi 'link', 'image', dan 'video' ke Toolbar Editor
const quillToolbar = [
    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
    ['bold', 'italic', 'underline', 'strike'], 
    [{ 'color': [] }, { 'background': [] }],
    [{ 'align': [] }],
    [{ 'list': 'ordered'}, { 'list': 'bullet' }], 
    ['link', 'image', 'video'],
    ['clean']
];
</script>

<template>
    <Head :title="isEdit ? 'Edit Halaman' : 'Buat Halaman'" />

    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">{{ isEdit ? 'Edit Halaman' : 'Buat Halaman Baru' }}</h3>
                <p class="text-muted mb-0">Buat konten kaya (rich text) dengan slug otomatis.</p>
            </div>
            <button @click="submit" class="btn btn-navy rounded-pill px-4" :disabled="form.processing">
                <i class="bi bi-check-lg me-2"></i> Simpan Halaman
            </button>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="nova-card p-4 h-100">
                    <h6 class="fw-bold text-navy mb-4">Konten Utama</h6>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Judul Halaman <span class="text-danger">*</span></label>
                        <input v-model="form.title" type="text" class="form-control form-control-lg" required placeholder="Ex: Kebijakan Privasi">
                        <small v-if="isEdit" class="text-muted mt-1 d-block">URL: /p/{{ page.slug }}</small>
                        <div v-if="form.errors.title" class="text-danger small mt-1">{{ form.errors.title }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Konten Halaman</label>
                        <div class="quill-wrapper border rounded">
                            <QuillEditor theme="snow" v-model:content="form.content" contentType="html" :toolbar="quillToolbar" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="nova-card p-4 mb-4">
                    <h6 class="fw-bold text-navy mb-3">Pengaturan Gambar</h6>
                    
                    <label class="form-label fw-bold">Gambar Utama (Hero)</label>
                    <div class="preview-box border rounded overflow-hidden bg-light mb-2 d-flex align-items-center justify-content-center" style="height: 180px;">
                        <img v-if="previewHero" :src="previewHero" class="w-100 h-100 object-fit-cover">
                        <div v-else class="text-muted small text-center">
                            <i class="bi bi-image fs-1 d-block mb-1"></i> Belum ada gambar
                        </div>
                    </div>
                    <input type="file" class="form-control" @change="handleHeroUpload" accept="image/*">
                    <small class="text-muted mt-1 d-block">Akan ditampilkan di atas/sebagai cover.</small>
                    <div v-if="form.errors.hero_image" class="text-danger small mt-1">{{ form.errors.hero_image }}</div>
                </div>

                <div class="nova-card p-4">
                    <h6 class="fw-bold text-navy mb-3">Pengaturan SEO & Meta</h6>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Publikasi</label>
                        <select v-model="form.is_active" class="form-select">
                            <option :value="1">Published (Tayang)</option>
                            <option :value="0">Draft (Sembunyikan)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Meta Title</label>
                        <input v-model="form.meta_title" type="text" class="form-control" placeholder="Opsional (default = judul)">
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Meta Description</label>
                        <textarea v-model="form.meta_description" class="form-control" rows="3" placeholder="Deskripsi singkat untuk SEO Google..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.nova-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
.text-navy { color: #002b49; }
.btn-navy { background-color: #002b49; color: #fff; border: none; transition: 0.3s; }
.btn-navy:hover { background-color: #00406b; transform: translateY(-2px); }
.object-fit-cover { object-fit: cover; }
.form-control:focus { border-color: #002b49; box-shadow: 0 0 0 3px rgba(0, 43, 73, 0.1); }

/* Editor Overrides */
.quill-wrapper { background-color: #fff; }
:deep(.ql-editor) { min-height: 400px; font-size: 1rem; }
:deep(.ql-toolbar) { border-radius: 8px 8px 0 0 !important; background-color: #f8f9fa; border-color: #dee2e6 !important; }
:deep(.ql-container) { border-radius: 0 0 8px 8px !important; border-color: #dee2e6 !important; }
</style>
