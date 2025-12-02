<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler
} from 'chart.js'
import { Bar, Line } from 'vue-chartjs'

// --- 1. REGISTRASI CHART ---
ChartJS.register(CategoryScale, LinearScale, BarElement, LineElement, PointElement, Title, Tooltip, Legend, Filler)

const props = defineProps({
    stats: Object,
    latest_projects: Array,
    chartData: Object,
    visitorChart: Object, 
    trafficSources: Array 
})

const today = new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' })

// --- 2. CONFIG CHART PROYEK (BAR) ---
// PERHATIKAN NAMA VARIABEL INI: projectChartConfig
const projectChartConfig = {
    data: {
        labels: props.chartData?.labels || [], // Pakai optional chaining biar aman
        datasets: [{
            label: 'Proyek',
            backgroundColor: '#002b49',
            borderRadius: 4,
            barThickness: 30,
            data: props.chartData?.values || []
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { display: true, color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
}

// --- 3. CONFIG CHART PENGUNJUNG (LINE) ---
const visitorChartConfig = {
    data: {
        labels: props.visitorChart?.labels || [],
        datasets: [{
            label: 'Pengunjung',
            backgroundColor: 'rgba(0, 43, 73, 0.1)',
            borderColor: '#002b49',
            borderWidth: 2,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#002b49',
            pointRadius: 4,
            fill: true,
            tension: 0.4,
            data: props.visitorChart?.data || []
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { display: false },
            x: { grid: { display: false }, ticks: { font: { size: 10 } } }
        }
    }
}
</script>

<template>
    <Head title="Dashboard Overview" />

    <AdminLayout>
        
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h4 class="fw-bold text-slate-800 mb-1">Dashboard</h4>
                <p class="text-muted mb-0 small">{{ today }} &bull; Overview Aktivitas</p>
            </div>
            <div>
                <Link href="/" target="_blank" class="btn btn-outline-navy rounded-pill px-3 btn-sm fw-bold">
                    <i class="bi bi-box-arrow-up-right me-2"></i> Lihat Website
                </Link>
            </div>
        </div>

        <div class="row g-4 mb-4">
            
            <div class="col-md-3">
                <div class="d-flex flex-column gap-4 h-100">
                    <div class="stat-card flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="stat-label">Total Proyek</p>
                                <h2 class="stat-value">{{ stats.total_projects }}</h2>
                            </div>
                            <div class="stat-icon bg-blue-subtle text-blue"><i class="bi bi-briefcase-fill"></i></div>
                        </div>
                        <div class="mt-3 pt-3 border-top border-light">
                            <small class="text-success fw-bold"><i class="bi bi-check-circle"></i> {{ stats.completed_projects }} Selesai</small>
                        </div>
                    </div>

                    <div class="stat-card flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="stat-label">Total Berita</p>
                                <h2 class="stat-value">{{ stats.total_news }}</h2>
                            </div>
                            <div class="stat-icon bg-orange-subtle text-orange"><i class="bi bi-newspaper"></i></div>
                        </div>
                        <div class="mt-3 pt-3 border-top border-light">
                            <small class="text-muted">Artikel terpublikasi</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="content-card h-100 position-relative overflow-hidden">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="fw-bold m-0 text-navy">Trafik Website</h6>
                            <small class="text-muted">Kunjungan 7 hari terakhir</small>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark">{{ stats.total_visitors.toLocaleString() }}</h3>
                    </div>
                    
                    <div class="chart-container-visit mt-3">
                        <Line :data="visitorChartConfig.data" :options="visitorChartConfig.options" />
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="content-card h-100">
                    <h6 class="fw-bold mb-4 text-navy">Sumber Kunjungan</h6>
                    
                    <div class="traffic-list">
                        <div v-for="(source, index) in trafficSources" :key="index" class="traffic-item mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small fw-bold text-dark">{{ source.name }}</span>
                                <span class="small text-muted">{{ source.percent }}%</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-navy" role="progressbar" 
                                    :style="{ width: source.percent + '%', opacity: 1 - (index * 0.2) }" 
                                    aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <Link href="#" class="small text-decoration-none text-muted hover-navy">Lihat Laporan Detail <i class="bi bi-chevron-right"></i></Link>
                    </div>
                </div>
            </div>

        </div>

        <div class="row g-4">
            
            <div class="col-lg-8">
                <div class="content-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold m-0">Proyek Terbaru</h6>
                        <Link href="#" class="btn btn-sm btn-light border">Kelola</Link>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle custom-table">
                            <thead>
                                <tr>
                                    <th class="ps-3">NAMA PROYEK</th>
                                    <th>KLIEN</th>
                                    <th>TAHUN</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="project in latest_projects" :key="project.id">
                                    <td class="ps-3"><span class="fw-bold text-dark">{{ project.title }}</span></td>
                                    <td class="text-muted small">{{ project.client }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ project.year }}</span></td>
                                    <td>
                                        <span class="badge rounded-pill fw-normal px-2" 
                                            :class="project.status === 'Completed' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'">
                                            {{ project.status }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="content-card h-100">
                    <h6 class="fw-bold mb-2">Pertumbuhan Proyek</h6>
                    <small class="text-muted d-block mb-4">Jumlah proyek dikerjakan per tahun</small>
                    <div class="chart-container-project">
                        <Bar :data="projectChartConfig.data" :options="projectChartConfig.options" />
                    </div>
                </div>
            </div>

        </div>

    </AdminLayout>
</template>

<style scoped>
/* --- COLORS --- */
.text-navy { color: #002b49; }
.bg-navy { background-color: #002b49; }
.btn-outline-navy { color: #002b49; border-color: #002b49; }
.btn-outline-navy:hover { background-color: #002b49; color: #fff; }

.bg-blue-subtle { background-color: #e0f2fe; }
.text-blue { color: #0284c7; }
.bg-orange-subtle { background-color: #ffedd5; }
.text-orange { color: #ea580c; }
.bg-white-subtle { background-color: rgba(255,255,255,0.2); }

/* --- CARDS --- */
.stat-card {
    background: #fff; border-radius: 12px; padding: 1.25rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.03);
}
.content-card {
    background: #fff; border-radius: 12px; padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.02);
}

.stat-label {
    font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 0.25rem; text-transform: uppercase;
}
.stat-value { font-size: 1.8rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1; }
.stat-icon {
    width: 42px; height: 42px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
}

/* --- CHARTS --- */
.chart-container-visit { height: 200px; width: 100%; }
.chart-container-project { height: 250px; width: 100%; }

/* --- TABLE --- */
.custom-table thead th {
    font-size: 0.7rem; font-weight: 700; color: #94a3b8;
    border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;
}
.custom-table tbody td {
    padding: 0.75rem 0.5rem; border-bottom: 1px solid #f8fafc; font-size: 0.85rem;
}
.custom-table tr:last-child td { border-bottom: none; }

/* --- TRAFFIC PROGRESS --- */
.progress { background-color: #f1f5f9; border-radius: 10px; }
.progress-bar { border-radius: 10px; }
.hover-navy:hover { color: #002b49 !important; text-decoration: underline !important; }
</style>