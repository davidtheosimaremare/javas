<script setup>
import { ref, watch } from 'vue'
import { Head, useForm, router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    news: Object,       // Pagination Data
    pageSetting: Object,
    filters: Object
})

const activeTab = ref('news-list')
const search = ref(props.filters.search || '')

// Search Logic
watch(search, (value) => {
    router.get('/admin/news-list', { search: value }, {
        preserveState: true, replace: true, preserveScroll: true
    })
})

// Form Header
const formSettings = useForm({
    _method: 'POST',
    hero_title: props.pageSetting?.hero_title || 'Berita & Artikel',
    hero_bg_path: null, 
})
const previewHeroBg = ref(props.pageSetting?.hero_bg_path ? getImgUrl(props.pageSetting.hero_bg_path) : null)

// Actions
const submitSettings = () => {
    formSettings.post('/admin/news-list/header', { preserveScroll: true, forceFormData: true })
}
const handleHeroUpload = (e) => {
    const file = e.target.files[0]; if(file) { formSettings.hero_bg_path = file; previewHeroBg.value = URL.createObjectURL(file); }
}
const deleteNews = (id) => {
    if(confirm('Hapus berita ini?')) router.delete(`/admin/news-list/${id}`)
}

function getImgUrl(path) {
    if (!path) return '/images/no-image.png'; 
    if (path.startsWith('http')) return path;
    return `/storage/${path.replace(/^\/?storage\//, '')}`;
}
</script>

<template>
    <Head title="Manajemen Berita" />

    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-slate-800 mb-0">Manajemen Berita</h3>
        </div>

        <div class="nav-tabs-wrapper mb-4">
            <button class="tab-btn" :class="{ active: activeTab === 'news-list' }" @click="activeTab = 'news-list'">
                <i class="bi bi-list-ul me-2"></i> Daftar Berita
            </button>
            <button class="tab-btn" :class="{ active: activeTab === 'settings' }" @click="activeTab = 'settings'">
                <i class="bi bi-sliders me-2"></i> Pengaturan Halaman
            </button>
        </div>

        <div v-if="activeTab === 'news-list'">
            <div class="nova-card">
                <div class="card-header bg-white p-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input v-model="search" type="text" class="form-control border-start-0 bg-light" placeholder="Cari judul berita...">
                    </div>
                    
                    <Link href="/admin/news-list/create" class="btn btn-navy rounded-pill px-4">
                        <i class="bi bi-plus-lg me-2"></i> Tulis Berita
                    </Link>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 custom-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">JUDUL</th>
                                <th>KATEGORI</th>
                                <th>TANGGAL</th>
                                <th class="text-end pe-4">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="news.data.length === 0">
                                <td colspan="4" class="text-center py-5 text-muted">Belum ada berita.</td>
                            </tr>
                            <tr v-for="item in news.data" :key="item.id">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img :src="getImgUrl(item.image)" class="rounded" style="width: 60px; height: 40px; object-fit: cover;">
                                        <div class="fw-bold text-dark text-wrap" style="max-width: 350px;">{{ item.title }}</div>
                                    </div>
                                </td>
                                <td><span class="badge bg-blue-subtle text-navy">{{ item.category }}</span></td>
                                <td class="text-muted small">{{ new Date(item.published_at).toLocaleDateString('id-ID') }}</td>
                                <td class="text-end pe-4">
                                    <Link :href="`/admin/news-list/${item.id}/edit`" class="btn btn-sm btn-light border me-2"><i class="bi bi-pencil"></i></Link>
                                    <button @click="deleteNews(item.id)" class="btn btn-sm btn-light border text-danger"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="p-3 d-flex justify-content-end" v-if="news.links.length > 3">
                     <nav>
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item" :class="{ disabled: !link.url, active: link.active }" v-for="(link, k) in news.links" :key="k">
                                <Link class="page-link" :href="link.url" v-html="link.label" />
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <div v-if="activeTab === 'settings'">
            <div class="nova-card p-4 border-start border-5 border-navy">
                <h6 class="fw-bold text-navy mb-3">Header Halaman Berita</h6>
                <form @submit.prevent="submitSettings">
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">JUDUL HALAMAN</label>
                            <input v-model="formSettings.hero_title" type="text" class="form-control fw-bold" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted">BACKGROUND BANNER</label>
                            <div class="d-flex align-items-center gap-3 border rounded p-3 bg-light">
                                <div class="preview-box border rounded overflow-hidden bg-secondary" style="width: 160px; height: 90px;">
                                    <img v-if="previewHeroBg" :src="previewHeroBg" class="w-100 h-100 object-fit-cover">
                                </div>
                                <input @change="handleHeroUpload" type="file" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-navy rounded-pill px-4" :disabled="formSettings.processing">Simpan Settings</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </AdminLayout>
</template>

<style scoped>
/* Reuse Nova Styles */
.nav-tabs-wrapper { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px; }
.tab-btn { border: 1px solid #e2e8f0; background: #fff; color: #64748b; padding: 8px 20px; border-radius: 50px; font-weight: 600; font-size: 0.9rem; transition: 0.3s; }
.tab-btn.active { background-color: #002b49; color: #fff; border-color: #002b49; box-shadow: 0 4px 6px rgba(0, 43, 73, 0.2); }
.nova-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
.text-navy { color: #002b49; }
.btn-navy { background-color: #002b49; color: #fff; border: none; }
.btn-navy:hover { background-color: #00406b; }
.bg-blue-subtle { background-color: #e0f2fe; }
.custom-table thead th { font-size: 0.8rem; font-weight: 700; color: #64748b; border-bottom: 1px solid #f1f5f9; }
.custom-table td { padding: 1rem; border-bottom: 1px solid #f8fafc; font-size: 0.9rem; }
.page-link { color: #002b49; }
.page-item.active .page-link { background-color: #002b49; border-color: #002b49; color: #fff; }
</style>