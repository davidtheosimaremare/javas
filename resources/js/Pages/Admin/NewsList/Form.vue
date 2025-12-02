<script setup>
import { ref, computed } from 'vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({ news: Object })
const isEdit = computed(() => !!props.news)

const form = useForm({
    _method: 'POST',
    title: props.news?.title || '',
    category: props.news?.category || '',
    published_at: props.news?.published_at || new Date().toISOString().substr(0, 10),
    summary: props.news?.summary || '',
    content: props.news?.content || '',
    image: null
})

const previewImage = ref(props.news?.image ? getImgUrl(props.news.image) : null)

function getImgUrl(path) {
    if (!path) return null;
    if (path.startsWith('http')) return path;
    return `/storage/${path.replace(/^\/?storage\//, '')}`;
}

const handleImageUpload = (e) => {
    const file = e.target.files[0]; 
    if(file) { form.image = file; previewImage.value = URL.createObjectURL(file); }
}

const submit = () => {
    const url = isEdit.value ? `/admin/news-list/${props.news.id}` : '/admin/news-list'
    form.post(url, { preserveScroll: true, forceFormData: true })
}

const quillToolbar = [['bold', 'italic', 'underline'], [{ 'list': 'ordered'}, { 'list': 'bullet' }], ['link', 'image'], ['clean']];
</script>

<template>
    <Head :title="isEdit ? 'Edit Berita' : 'Tulis Berita'" />

    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-slate-800 mb-0">{{ isEdit ? 'Edit Berita' : 'Tulis Berita Baru' }}</h3>
            <Link href="/admin/news-list" class="btn btn-light border rounded-pill px-4">Batal</Link>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="nova-card p-5">
                    <form @submit.prevent="submit">
                        
                        <div class="row g-4">
                            <div class="col-md-8">
                                <label class="form-label fw-bold text-muted small">JUDUL ARTIKEL</label>
                                <input v-model="form.title" type="text" class="form-control form-control-lg" required>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted small">KATEGORI</label>
                                <select v-model="form.category" class="form-select form-select-lg">
                                    <option value="Berita">Berita</option>
                                    <option value="CSR">CSR</option>
                                    <option value="Safety">Safety</option>
                                    <option value="Teknologi">Teknologi</option>
                                    <option value="Event">Event</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted small">TANGGAL</label>
                                <input v-model="form.published_at" type="date" class="form-control form-control-lg">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small">RINGKASAN (SUMMARY)</label>
                                <textarea v-model="form.summary" class="form-control" rows="2" maxlength="250" placeholder="Ringkasan singkat untuk preview..."></textarea>
                                <div class="text-end text-muted small">{{ form.summary.length }}/250</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small">THUMBNAIL IMAGE (16:9)</label>
                                <div class="d-flex align-items-center gap-4 border rounded p-3 bg-light">
                                    <div class="preview-box rounded overflow-hidden bg-secondary" style="width: 200px; height: 112px;">
                                        <img v-if="previewImage" :src="previewImage" class="w-100 h-100 object-fit-cover">
                                        <div v-else class="w-100 h-100 d-flex align-items-center justify-content-center text-white-50 small">No Img</div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <input @change="handleImageUpload" type="file" class="form-control" accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small">KONTEN ARTIKEL</label>
                                <div class="quill-wrapper border rounded">
                                    <QuillEditor theme="snow" v-model:content="form.content" contentType="html" :toolbar="quillToolbar" />
                                </div>
                            </div>

                            <div class="col-12 pt-3 border-top text-end">
                                <button type="submit" class="btn btn-navy rounded-pill px-5 py-2" :disabled="form.processing">
                                    {{ form.processing ? 'Menyimpan...' : 'Terbitkan Berita' }}
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.nova-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
.text-navy { color: #002b49; }
.btn-navy { background-color: #002b49; color: #fff; border: none; transition: 0.3s; }
.btn-navy:hover { background-color: #00406b; }
.quill-wrapper { background: #fff; }
:deep(.ql-editor) { min-height: 400px; font-size: 1rem; line-height: 1.8; }
:deep(.ql-toolbar) { border-radius: 8px 8px 0 0 !important; background-color: #f8f9fa; border-color: #dee2e6 !important; }
:deep(.ql-container) { border-radius: 0 0 8px 8px !important; border-color: #dee2e6 !important; }
.object-fit-cover { object-fit: cover; }
</style>