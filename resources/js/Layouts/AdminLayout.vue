<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

// --- STATE ---
const isSidebarOpen = ref(true)
const isMobile = ref(false)
const searchQuery = ref('')

// --- LOGIC HELPER: CEK URL AKTIF ---
// Fungsi ini mengecek apakah URL browser saat ini berawalan dengan path tertentu
const page = usePage()
const isActive = (path) => page.url.startsWith(path)

// --- LOGIC RESPONSIVE ---
const checkScreenSize = () => {
    isMobile.value = window.innerWidth < 1024 
    if (isMobile.value) {
        isSidebarOpen.value = false 
    } else {
        isSidebarOpen.value = true 
    }
}

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
}

const handleNavClick = () => {
    if (isMobile.value) isSidebarOpen.value = false
}

const logout = () => {
    router.post('/admin/logout')
}

onMounted(() => {
    checkScreenSize()
    window.addEventListener('resize', checkScreenSize)
})

onUnmounted(() => {
    window.removeEventListener('resize', checkScreenSize)
})
</script>

<template>
    <div class="admin-layout">
        
        <div 
            class="sidebar-backdrop" 
            :class="{ 'show': isMobile && isSidebarOpen }"
            @click="isSidebarOpen = false"
        ></div>

        <aside class="sidebar" :class="{ 'closed': !isSidebarOpen }">
            
            <div class="sidebar-header">
                <div class="brand-wrapper">
                    <div class="logo-box">J</div>
                    <div class="brand-text">
                        <span class="fw-bold">JBB</span>
                        <span class="badge-admin">Admin</span>
                    </div>
                </div>
            </div>

            <div class="sidebar-content custom-scroll">
                <ul class="nav flex-column gap-1">
                    
                    <li class="nav-label">MAIN MENU</li>

                    <li class="nav-item">
                        <Link href="/admin/dashboard" class="nav-link" :class="{ 'active': isActive('/admin/dashboard') }" @click="handleNavClick">
                            <i class="bi bi-grid-fill icon"></i>
                            <span class="label">Dashboard</span>
                        </Link>
                    </li>

                    <li class="nav-item">
                        <Link href="/admin/company-profile" class="nav-link" :class="{ 'active': isActive('/admin/company-profile') }" @click="handleNavClick">
                            <i class="bi bi-building-fill icon"></i>
                            <span class="label">Company Profile</span>
                        </Link>
                    </li>

                    <li class="nav-label mt-4">CONTENT MANAGEMENT</li>

                    <li class="nav-item">
                   <a class="nav-link has-arrow" 
                    :class="{ 
                        'collapsed': !isActive('/admin/home-editor') && !isActive('/admin/about-editor'), 
                        'active': isActive('/admin/home-editor') && isActive('/admin/about-editor') 
                    }" 
                    data-bs-toggle="collapse" 
                    href="#menuPages" 
                    role="button" 
                    :aria-expanded="(isActive('/admin/home-editor') || isActive('/admin/about-editor')) ? 'true' : 'false'">
                        
                        <i class="bi bi-layout-text-window-reverse icon"></i>
                        <span class="label">Halaman Web</span>
                        <i class="bi bi-chevron-down ms-auto arrow"></i>
                    </a>
                        
                        <div class="collapse" 
                             :class="{ 'show': isActive('/admin/home-editor') || isActive('/admin/about-editor') || isActive('/admin/project-editor') || isActive('/admin/career-editor') || isActive('/admin/news-editor') || isActive('/admin/contact-editor') }" 
                             id="menuPages"
                             data-bs-parent="#menuAccordion">
                            <ul class="nav flex-column sub-menu">
                                
                                <li>
                                    <Link 
                                        href="/admin/home-editor" 
                                        class="sub-link" 
                                        :class="{ 'active': isActive('/admin/home-editor') }" 
                                        @click="handleNavClick"
                                    >
                                        Homepage
                                    </Link>
                                </li>

                                <li><Link href="/admin/about-editor" 
                                        class="sub-link" 
                                        :class="{ 'active': isActive('/admin/about-editor') }" 
                                        @click="handleNavClick">About Us</Link></li>
                                <li>
                                    <Link 
                                        href="/admin/project-editor" 
                                        class="sub-link" 
                                        :class="{ 'active': isActive('/admin/project-editor') }" 
                                        @click="handleNavClick">
                                        Projects
                                    </Link>
                                </li>
                                <li>
                                    <Link 
                                        href="/admin/career-editor" 
                                        class="sub-link" 
                                        :class="{ 'active': isActive('/admin/career-editor') }" 
                                        @click="handleNavClick"
                                    >
                                        Career
                                    </Link>
                                </li>
                                <li>
                                        <Link 
                                            href="/admin/news-editor" 
                                            class="sub-link" 
                                            :class="{ 'active': isActive('/admin/news-editor') }" 
                                            @click="handleNavClick"
                                        >
                                            News
                                        </Link>
                                    </li>
                                <li>
    <Link 
        href="/admin/contact-editor" 
        class="sub-link" 
        :class="{ 'active': isActive('/admin/contact-editor') }" 
        @click="handleNavClick"
    >
        Contact
    </Link>
</li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                         <Link href="/admin/projects" class="nav-link" :class="{ 'active': isActive('/admin/projects') }" @click="handleNavClick">
                            <i class="bi bi-briefcase-fill icon"></i>
                            <span class="label">Manajemen Proyek</span>
                        </Link>
                    </li>

                    <li class="nav-item">
                         <Link href="/admin/news-list" class="nav-link" :class="{ 'active': isActive('/admin/news-list') }" @click="handleNavClick">
                            <i class="bi bi-newspaper icon"></i>
                            <span class="label">Berita & Artikel</span>
                        </Link>
                    </li>


                    <li class="nav-item">
                         <Link href="/admin/career-list" class="nav-link" :class="{ 'active': isActive('/admin/career-list') }" @click="handleNavClick">
                            <i class="bi bi-newspaper icon"></i>
                            <span class="label">Karir</span>
                        </Link>
                    </li>

                    <li class="nav-label mt-4">Project Management</li>

                    <li class="nav-item">
                        <Link href="#" class="nav-link" @click="handleNavClick">
                            <i class="bi bi-newspaper icon"></i>
                            <span class="label">Project Timeline</span>
                        </Link>
                    </li>

                    <li class="nav-item">
                        <Link href="#" class="nav-link" @click="handleNavClick">
                            <i class="bi bi-newspaper icon"></i>
                            <span class="label">Team</span>
                        </Link>
                    </li>


                    

                    <li class="nav-label mt-4">SYSTEM</li>

                    <li class="nav-item">
                        <Link href="#" class="nav-link" @click="handleNavClick">
                            <i class="bi bi-people-fill icon"></i>
                            <span class="label">Administrator</span>
                        </Link>
                    </li>

                    <li class="nav-item mt-4 d-lg-none">
                        <button class="nav-link text-danger w-100 text-start" @click="logout">
                            <i class="bi bi-box-arrow-left icon"></i> Logout
                        </button>
                    </li>
                </ul>
            </div>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="avatar">A</div>
                    <div class="info">
                        <div class="name">Administrator</div>
                        <div class="role">Super User</div>
                    </div>
                </div>
                <button @click="logout" class="btn-logout" title="Logout">
                    <i class="bi bi-power"></i>
                </button>
            </div>
        </aside>

        <div class="main-wrapper" :class="{ 'full-width': !isSidebarOpen }">
            
            <header class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn-toggle" @click="toggleSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="global-search d-none d-md-flex">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" v-model="searchQuery" placeholder="Tekan / untuk mencari resource..." class="search-input">
                        <span class="search-shortcut">/</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <button class="btn-icon">
                        <i class="bi bi-bell"></i>
                        <span class="badge-dot"></span>
                    </button>
                    <div class="dropdown">
                        <a href="#" class="user-dropdown" data-bs-toggle="dropdown">
                            <div class="avatar-sm">A</div>
                            <span class="d-none d-md-block ms-2 fw-bold text-dark">Admin JBB</span>
                            <i class="bi bi-chevron-down ms-2 fs-xs text-muted"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-3 mt-2">
                            <li><button class="dropdown-item rounded-2 text-danger" @click="logout">Logout</button></li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="content-body">
                <slot />
            </main>

            <footer class="footer">
                <p>&copy; 2025 PT JBB Javas Berkah Bistari. <span class="text-muted">v1.0.0</span></p>
            </footer>

        </div>

    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Rethink+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap');

:root {
    --nova-primary: #002b49;       
    --nova-primary-dark: #001a2e;  
    --nova-bg: #f3f4f6;            
    --nova-sidebar-width: 280px;   
    --nova-header-height: 64px;    
    --text-menu: #94a3b8;          
    --text-menu-active: #ffffff;   
}

body { font-family: 'Rethink Sans', sans-serif !important; background-color: var(--nova-bg); }
.admin-layout { display: flex; min-height: 100vh; width: 100%; }

/* SIDEBAR STYLE */
.sidebar {
    width: var(--nova-sidebar-width); height: 100vh; position: fixed; top: 0; left: 0;
    background-color: var(--nova-primary); color: white; z-index: 1050;
    display: flex; flex-direction: column; transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    box-shadow: 4px 0 20px rgba(0,0,0,0.1);
}
.sidebar-header { height: var(--nova-header-height); display: flex; align-items: center; padding: 0 1.5rem; background-color: rgba(0,0,0,0.1); border-bottom: 1px solid rgba(255,255,255,0.05); }
.brand-wrapper { display: flex; align-items: center; gap: 12px; }
.logo-box { width: 36px; height: 36px; background: #fff; color: var(--nova-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.2rem; }
.brand-text { font-size: 1.1rem; letter-spacing: 0.5px; line-height: 1; }
.badge-admin { display: block; font-size: 0.65rem; text-transform: uppercase; color: rgba(255,255,255,0.5); font-weight: 600; letter-spacing: 1px; margin-top: 2px; }
.sidebar-content { flex: 1; overflow-y: auto; padding: 1.5rem 1rem; }
.nav-label { font-size: 0.7rem; font-weight: 700; color: rgba(255,255,255,0.4); margin: 1.5rem 0 0.5rem 0.8rem; letter-spacing: 0.05em; }

/* Menu Links */
.nav-link { display: flex; align-items: center; padding: 0.75rem 1rem; color: var(--text-menu); font-weight: 500; font-size: 0.95rem; border-radius: 8px; margin-bottom: 2px; transition: all 0.2s; }
.nav-link .icon { font-size: 1.1rem; margin-right: 12px; width: 20px; text-align: center; opacity: 0.8; }
.nav-link:hover { color: #fff; background-color: rgba(255,255,255,0.08); }
.nav-link.active { background-color: rgba(255,255,255,0.15); color: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.2); font-weight: 600; }

/* Submenu */
.nav-link.collapsed .arrow { transform: rotate(0deg); }
.nav-link .arrow { transition: transform 0.3s; font-size: 0.8rem; opacity: 0.7; }
.nav-link:not(.collapsed) .arrow { transform: rotate(180deg); }
.sub-menu { padding-left: 2.8rem; padding-top: 0.25rem; }
.sub-link { display: block; color: var(--text-menu); font-size: 0.9rem; padding: 0.4rem 0; text-decoration: none; transition: color 0.2s; position: relative; }
.sub-link:hover { color: #fff; }

/* Submenu Active State (Highlight Putih) */
.sub-link.active { color: #fff; font-weight: 600; }
.sub-link.active::before { content: "•"; position: absolute; left: -15px; color: #4dabf7; font-size: 1.2rem; line-height: 1.2rem; }

/* Sidebar Footer */
.sidebar-footer { padding: 1rem 1.5rem; background-color: rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: space-between; }
.user-profile { display: flex; align-items: center; gap: 10px; }
.avatar { width: 38px; height: 38px; background: #3b82f6; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; border: 2px solid rgba(255,255,255,0.1); }
.info .name { font-size: 0.9rem; font-weight: 600; color: #fff; line-height: 1.2; }
.info .role { font-size: 0.75rem; color: rgba(255,255,255,0.5); }
.btn-logout { background: transparent; border: none; color: rgba(255,255,255,0.5); font-size: 1.2rem; cursor: pointer; transition: 0.2s; }
.btn-logout:hover { color: #ef4444; transform: scale(1.1); }

/* Main Wrapper */
.main-wrapper { flex-grow: 1; margin-left: var(--nova-sidebar-width); min-height: 100vh; display: flex; flex-direction: column; transition: margin-left 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); background-color: var(--nova-bg); width: calc(100% - var(--nova-sidebar-width)); }
@media (min-width: 1024px) { .sidebar.closed { transform: translateX(-100%); } .main-wrapper.full-width { margin-left: 0; width: 100%; } }

/* Topbar */
.topbar { height: var(--nova-header-height); background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 2rem; position: sticky; top: 0; z-index: 1040; }
.btn-toggle { background: transparent; border: none; font-size: 1.5rem; color: #64748b; cursor: pointer; transition: color 0.2s; padding: 0; }
.btn-toggle:hover { color: var(--nova-primary); }
.global-search { position: relative; width: 400px; margin-left: 1rem; }
.search-input { width: 100%; background-color: #f1f5f9; border: 1px solid transparent; border-radius: 8px; padding: 0.5rem 2.5rem; font-size: 0.9rem; color: #334155; transition: all 0.2s; }
.search-input:focus { background-color: #fff; border-color: #cbd5e1; box-shadow: 0 0 0 3px rgba(0, 43, 73, 0.1); outline: none; }
.search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem; }
.search-shortcut { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: #e2e8f0; color: #64748b; font-size: 0.7rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; }
.btn-icon { position: relative; background: transparent; border: none; color: #64748b; font-size: 1.25rem; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
.btn-icon:hover { background-color: #f1f5f9; color: var(--nova-primary); }
.badge-dot { position: absolute; top: 10px; right: 12px; width: 8px; height: 8px; background-color: #ef4444; border-radius: 50%; border: 2px solid #fff; }
.user-dropdown { display: flex; align-items: center; text-decoration: none; }
.avatar-sm { width: 34px; height: 34px; background: var(--nova-primary); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; }
.content-body { padding: 2rem; flex-grow: 1; }
.footer { padding: 1.5rem 2rem; text-align: center; color: #94a3b8; font-size: 0.85rem; border-top: 1px solid #e2e8f0; }

@media (max-width: 1023.98px) {
    .sidebar { transform: translateX(-100%); box-shadow: none; }
    .sidebar:not(.closed) { transform: translateX(0); box-shadow: 5px 0 25px rgba(0,0,0,0.5); }
    .main-wrapper { margin-left: 0 !important; width: 100% !important; }
    .topbar { padding: 0 1rem; }
    .sidebar-backdrop { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 1045; backdrop-filter: blur(2px); opacity: 0; visibility: hidden; transition: 0.3s; }
    .sidebar-backdrop.show { opacity: 1; visibility: visible; }
}
.custom-scroll::-webkit-scrollbar { width: 5px; }
.custom-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
.custom-scroll::-webkit-scrollbar-track { background: transparent; }
</style>