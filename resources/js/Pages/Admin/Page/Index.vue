<script setup>
import { Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({ pages: Object, filters: Object })

const searchForm = useForm({
    search: ''
})

const getImgUrl = (path) => {
    if (!path) return '/images/no-image.png';
    if (path.startsWith('http')) return path;
    return path.startsWith('/storage') ? path : `/storage/${path.replace(/^storage\//, '')}`;
}

const deletePage = (id) => {
    if(confirm('Hapus halaman ini secara permanen?')) {
        router.delete(`/admin/pages/${id}`)
    }
}
</script>

<template>
    <Head title="Manajemen Halaman" />

    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-slate-800 mb-1">Page Creator</h3>
                <p class="text-muted mb-0">Buat dan kelola halaman kustom untuk website.</p>
            </div>
            
            <Link href="/admin/pages/create" class="btn btn-navy rounded-pill px-4">
                <i class="bi bi-plus-lg me-2"></i> Buat Halaman
            </Link>
        </div>

        <div class="nova-card overflow-hidden">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <form @submit.prevent="router.get('/admin/pages', { search: searchForm.search })" class="d-flex align-items-center bg-light rounded-pill px-3 py-1" style="width: 300px;">
                    <i class="bi bi-search text-muted"></i>
                    <input v-model="searchForm.search" type="text" class="form-control border-0 bg-transparent shadow-none" placeholder="Cari halaman...">
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 custom-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">JUDUL HALAMAN</th>
                            <th>SLUG / URL</th>
                            <th>STATUS</th>
                            <th>DIBUAT</th>
                            <th class="text-end pe-4">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="pages.data.length === 0">
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada halaman yang dibuat.</td>
                        </tr>
                        <tr v-for="page in pages.data" :key="page.id">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img :src="getImgUrl(page.hero_image)" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                    <div class="fw-bold text-dark">{{ page.title }}</div>
                                </div>
                            </td>
                            <td class="text-muted"><a :href="`/p/${page.slug}`" target="_blank" class="text-primary text-decoration-none">/p/{{ page.slug }}</a></td>
                            <td>
                                <span class="badge rounded-pill fw-normal px-3" 
                                    :class="page.is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'">
                                    {{ page.is_active ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td>{{ new Date(page.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' }) }}</td>
                            <td class="text-end pe-4">
                                <Link :href="`/admin/pages/${page.id}/edit`" class="btn btn-sm btn-light border me-2" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </Link>
                                <button @click="deletePage(page.id)" class="btn btn-sm btn-light border text-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="p-3 d-flex justify-content-end" v-if="pages.links.length > 3">
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item" :class="{ disabled: !link.url, active: link.active }" v-for="(link, k) in pages.links" :key="k">
                            <Link class="page-link" :href="link.url" v-html="link.label" />
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.nova-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05); }
.btn-navy { background-color: #002b49; color: #fff; border: none; transition: 0.3s; }
.btn-navy:hover { background-color: #00406b; transform: translateY(-2px); }
.bg-success-subtle { background-color: #d1e7dd; color: #0f5132; }
.bg-secondary-subtle { background-color: #e2e3e5; color: #41464b; }
.custom-table thead th { font-size: 0.75rem; font-weight: 700; color: #64748b; border-bottom: 1px solid #f1f5f9; padding: 1rem; }
.custom-table td { padding: 1rem; border-bottom: 1px solid #f8fafc; font-size: 0.9rem; }
.page-link { color: #002b49; }
.page-item.active .page-link { background-color: #002b49; border-color: #002b49; color: #fff; }
</style>
