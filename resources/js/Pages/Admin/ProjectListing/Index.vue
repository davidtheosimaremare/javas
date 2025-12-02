<script setup>
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// Props dari Controller
defineProps({ projects: Object })

// Helper Image (Opsional, jika ada path aneh)
const getImgUrl = (path) => {
    if (!path) return '/images/no-image.png';
    if (path.startsWith('http')) return path;
    return path.startsWith('/storage') ? path : `/storage/${path.replace(/^storage\//, '')}`;
}

const deleteProject = (id) => {
    if(confirm('Hapus proyek ini beserta galeri dan testimoninya?')) {
        router.delete(`/admin/projects/${id}`)
    }
}
</script>

<template>
    <Head title="Manajemen Proyek" />

    <AdminLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-slate-800 mb-0">Daftar Proyek</h3>
            
            <Link href="/admin/projects/create" class="btn btn-navy rounded-pill px-4">
                <i class="bi bi-plus-lg me-2"></i> Tambah Proyek
            </Link>
        </div>

        <div class="nova-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 custom-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">PROYEK</th>
                            <th>KLIEN</th>
                            <th>STATUS</th>
                            <th>TAHUN</th>
                            <th class="text-center">FEATURED</th>
                            <th class="text-end pe-4">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="projects.data.length === 0">
                            <td colspan="6" class="text-center py-5 text-muted">Belum ada data proyek.</td>
                        </tr>
                        <tr v-for="project in projects.data" :key="project.id">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img :src="getImgUrl(project.hero_image)" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ project.title }}</div>
                                        <small class="text-muted">{{ project.category }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ project.client }}</td>
                            <td>
                                <span class="badge rounded-pill fw-normal px-3" 
                                    :class="project.status === 'Completed' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'">
                                    {{ project.status }}
                                </span>
                            </td>
                            <td>{{ project.year }}</td>
                            
                            <td class="text-center">
                                <span v-if="project.is_featured" class="badge bg-primary-subtle text-primary">Featured</span>
                                <span v-else class="text-muted small">-</span>
                            </td>

                            <td class="text-end pe-4">
                                <Link :href="`/admin/projects/${project.id}/edit`" class="btn btn-sm btn-light border me-2" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </Link>
                                <button @click="deleteProject(project.id)" class="btn btn-sm btn-light border text-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="p-3 d-flex justify-content-end" v-if="projects.links.length > 3">
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item" :class="{ disabled: !link.url, active: link.active }" v-for="(link, k) in projects.links" :key="k">
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
.bg-warning-subtle { background-color: #fff3cd; color: #664d03; }
.bg-primary-subtle { background-color: #cfe2ff; color: #084298; }
.custom-table thead th { font-size: 0.75rem; font-weight: 700; color: #64748b; border-bottom: 1px solid #f1f5f9; padding: 1rem; }
.custom-table td { padding: 1rem; border-bottom: 1px solid #f8fafc; font-size: 0.9rem; }
.page-link { color: #002b49; }
.page-item.active .page-link { background-color: #002b49; border-color: #002b49; color: #fff; }
</style>