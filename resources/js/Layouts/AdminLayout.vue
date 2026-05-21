<script setup>
import { ref, onMounted, onUnmounted, watch, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

// --- STATE ---
const isSidebarOpen = ref(true)
const isMobile = ref(false)
const searchQuery = ref('')
const isPagesOpen = ref(false)
const isDropdownOpen = ref(false)

// --- PAGE & LOGO ---
const page = usePage()
const logoUrl = computed(() => {
    const path = page.props.globalData?.profile?.logo_secondary || page.props.globalData?.profile?.logo_primary;
    if (path && path.startsWith('http')) return path
    if (path) return `/storage/${path.replace('storage/', '')}`
    return '/images/logo.png' // Fallback to default logo
})

const appName = computed(() => page.props.globalData?.profile?.name || 'JBB')

// --- LOGIC HELPER: CEK URL AKTIF ---
const isActive = (path) => page.url.startsWith(path)

const checkPagesActive = () => {
    return isActive('/admin/home-editor') || 
           isActive('/admin/about-editor') || 
           isActive('/admin/project-editor') || 
           isActive('/admin/career-editor') || 
           isActive('/admin/news-editor') || 
           isActive('/admin/contact-editor')
}

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

// Watch page.url to automatically open/close submenu on navigation
watch(() => page.url, () => {
    if (checkPagesActive()) {
        isPagesOpen.value = true
    }
    isDropdownOpen.value = false
})

onMounted(() => {
    isPagesOpen.value = checkPagesActive()
    checkScreenSize()
    window.addEventListener('resize', checkScreenSize)
})

onUnmounted(() => {
    window.removeEventListener('resize', checkScreenSize)
})
</script>

<template>
    <div class="admin-layout">
        
        <!-- Mobile Backdrop -->
        <div 
            class="sidebar-backdrop" 
            :class="{ 'show': isMobile && isSidebarOpen }"
            @click="isSidebarOpen = false"
        ></div>

        <!-- Sidebar -->
        <aside class="sidebar modern-sidebar" :class="{ 'closed': !isSidebarOpen }">
            
            <div class="sidebar-header">
                <div class="brand-wrapper">
                    <div class="logo-box">
                        <img :src="logoUrl" alt="Logo" class="brand-img" />
                    </div>
                    <div class="brand-text">
                        <span class="fw-bold text-white">{{ appName }}</span>
                        <span class="badge-admin">Admin Workspace</span>
                    </div>
                </div>
            </div>

            <div class="sidebar-content custom-scroll">
                <ul class="nav-menu">
                    
                    <li class="nav-label">MAIN MENU</li>

                    <li class="nav-item">
                        <Link href="/admin/dashboard" class="nav-link" :class="{ 'active': isActive('/admin/dashboard') }" @click="handleNavClick">
                            <div class="icon-wrapper"><i class="bi bi-grid"></i></div>
                            <span class="label">Dashboard</span>
                        </Link>
                    </li>

                    <li class="nav-item">
                        <Link href="/admin/company-profile" class="nav-link" :class="{ 'active': isActive('/admin/company-profile') }" @click="handleNavClick">
                            <div class="icon-wrapper"><i class="bi bi-building"></i></div>
                            <span class="label">Company Profile</span>
                        </Link>
                    </li>

                    <li class="nav-label">CONTENT MANAGEMENT</li>

                    <li class="nav-item has-submenu" :class="{ 'open': isPagesOpen }">
                        <div class="nav-link submenu-toggle" :class="{ 'active': checkPagesActive() }" @click="isPagesOpen = !isPagesOpen">
                            <div class="icon-wrapper"><i class="bi bi-layout-text-window-reverse"></i></div>
                            <span class="label">Halaman Web</span>
                            <i class="bi bi-chevron-down ms-auto arrow"></i>
                        </div>
                        
                        <div class="submenu-container" :style="{ maxHeight: isPagesOpen ? '350px' : '0' }">
                            <ul class="sub-menu">
                                <li>
                                    <Link href="/admin/home-editor" class="sub-link" :class="{ 'active': isActive('/admin/home-editor') }" @click="handleNavClick">
                                        Homepage
                                    </Link>
                                </li>
                                <li>
                                    <Link href="/admin/about-editor" class="sub-link" :class="{ 'active': isActive('/admin/about-editor') }" @click="handleNavClick">
                                        About Us
                                    </Link>
                                </li>
                                <li>
                                    <Link href="/admin/project-editor" class="sub-link" :class="{ 'active': isActive('/admin/project-editor') }" @click="handleNavClick">
                                        Projects
                                    </Link>
                                </li>
                                <li>
                                    <Link href="/admin/career-editor" class="sub-link" :class="{ 'active': isActive('/admin/career-editor') }" @click="handleNavClick">
                                        Career
                                    </Link>
                                </li>
                                <li>
                                    <Link href="/admin/news-editor" class="sub-link" :class="{ 'active': isActive('/admin/news-editor') }" @click="handleNavClick">
                                        News
                                    </Link>
                                </li>
                                <li>
                                    <Link href="/admin/contact-editor" class="sub-link" :class="{ 'active': isActive('/admin/contact-editor') }" @click="handleNavClick">
                                        Contact
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                         <Link href="/admin/projects" class="nav-link" :class="{ 'active': isActive('/admin/projects') }" @click="handleNavClick">
                            <div class="icon-wrapper"><i class="bi bi-briefcase"></i></div>
                            <span class="label">Manajemen Proyek</span>
                        </Link>
                    </li>

                    <li class="nav-item">
                         <Link href="/admin/news-list" class="nav-link" :class="{ 'active': isActive('/admin/news-list') }" @click="handleNavClick">
                            <div class="icon-wrapper"><i class="bi bi-newspaper"></i></div>
                            <span class="label">Berita & Artikel</span>
                        </Link>
                    </li>

                    <li class="nav-item">
                         <Link href="/admin/pages" class="nav-link" :class="{ 'active': isActive('/admin/pages') }" @click="handleNavClick">
                            <div class="icon-wrapper"><i class="bi bi-file-earmark-richtext"></i></div>
                            <span class="label">Page Creator</span>
                        </Link>
                    </li>

                    <li class="nav-item">
                         <Link href="/admin/career-list" class="nav-link" :class="{ 'active': isActive('/admin/career-list') }" @click="handleNavClick">
                            <div class="icon-wrapper"><i class="bi bi-person-workspace"></i></div>
                            <span class="label">Karir</span>
                        </Link>
                    </li>

                    <li class="nav-label">PROJECT MANAGEMENT</li>

                    <li class="nav-item">
                        <a href="#" class="nav-link" @click.prevent="handleNavClick">
                            <div class="icon-wrapper"><i class="bi bi-kanban"></i></div>
                            <span class="label">Project Timeline</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="#" class="nav-link" @click.prevent="handleNavClick">
                            <div class="icon-wrapper"><i class="bi bi-people"></i></div>
                            <span class="label">Team</span>
                        </a>
                    </li>

                    <li class="nav-label">SYSTEM</li>

                    <li class="nav-item">
                        <a href="#" class="nav-link" @click.prevent="handleNavClick">
                            <div class="icon-wrapper"><i class="bi bi-person-badge"></i></div>
                            <span class="label">Administrator</span>
                        </a>
                    </li>

                    <li class="nav-item mt-4 d-lg-none">
                        <button class="nav-link text-danger border-0 bg-transparent w-100 text-start" @click="logout">
                            <div class="icon-wrapper"><i class="bi bi-box-arrow-left"></i></div> 
                            <span class="label">Logout</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
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

        <!-- Main Content -->
        <div class="main-wrapper" :class="{ 'full-width': !isSidebarOpen }">
            
            <header class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn-toggle" @click="toggleSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="global-search d-none d-md-flex">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" v-model="searchQuery" placeholder="Cari menu atau resource..." class="search-input">
                        <span class="search-shortcut">/</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <button class="btn-icon">
                        <i class="bi bi-bell"></i>
                        <span class="badge-dot"></span>
                    </button>
                    <div class="dropdown">
                        <button type="button" class="user-dropdown border-0 bg-transparent d-flex align-items-center" @click="isDropdownOpen = !isDropdownOpen">
                            <div class="avatar-sm">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <span class="d-none d-md-block ms-2 fw-semibold text-dark">Admin</span>
                            <i class="bi bi-chevron-down ms-2 fs-xs text-muted"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-3 mt-2" :class="{ 'show': isDropdownOpen }" style="position: absolute; right: 0;">
                            <li><button class="dropdown-item rounded-2 text-danger" @click="logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</button></li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="content-body">
                <slot />
            </main>

            <footer class="footer">
                <p>&copy; 2025 PT JBB Javas Berkah Bistari. <span class="text-muted ms-2">v1.1.0</span></p>
            </footer>

        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

:root {
    --admin-bg: #f8fafc;
    --sidebar-bg: #0b1120; /* Sleek darker slate for a cleaner look */
    --sidebar-width: 280px;
    --header-height: 72px;
    --accent-color: #2563eb; /* Vibrant royal blue */
    --text-muted: #94a3b8;
    --text-light: #f8fafc;
}

body { 
    font-family: 'Plus Jakarta Sans', sans-serif !important; 
    background-color: var(--admin-bg); 
    margin: 0;
    padding: 0;
}

.admin-layout { 
    display: flex; 
    min-height: 100vh; 
    width: 100%; 
    overflow-x: hidden;
}

/* SIDEBAR MODERN */
.modern-sidebar {
    width: var(--sidebar-width); 
    height: 100vh; 
    position: fixed; 
    top: 0; 
    left: 0;
    background-color: var(--sidebar-bg); 
    color: var(--text-light); 
    z-index: 1050;
    display: flex; 
    flex-direction: column; 
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 4px 0 24px rgba(0,0,0,0.05);
}

.sidebar-header { 
    height: var(--header-height); 
    display: flex; 
    align-items: center; 
    padding: 0 1.5rem; 
    border-bottom: 1px solid rgba(255,255,255,0.08); 
}

.brand-wrapper { 
    display: flex; 
    align-items: center; 
    gap: 14px; 
}

.logo-box { 
    width: 42px; 
    height: 42px; 
    background: #ffffff; 
    border-radius: 10px; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.brand-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 5px;
}

.brand-text { 
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.brand-text .fw-bold {
    font-size: 1.15rem; 
    letter-spacing: -0.02em; 
    line-height: 1.1;
}

.badge-admin { 
    font-size: 0.65rem; 
    text-transform: uppercase; 
    color: #60a5fa; 
    font-weight: 700; 
    letter-spacing: 0.5px; 
    margin-top: 2px;
}

.sidebar-content { 
    flex: 1; 
    overflow-y: auto; 
    padding: 1.5rem 1.25rem; 
}

.nav-menu {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 6px; /* slightly bigger gap between items */
}

.nav-label { 
    font-size: 0.65rem; 
    font-weight: 800; 
    color: rgba(255,255,255,0.3); 
    margin: 1.5rem 0 0.5rem 0; 
    padding-left: 1rem;
    letter-spacing: 0.1em; 
}

/* Nav Links */
.nav-link { 
    display: flex !important; 
    flex-direction: row !important;
    align-items: center !important; 
    padding: 0.75rem 1rem; 
    color: var(--text-muted); 
    font-weight: 500; 
    font-size: 0.95rem; 
    border-radius: 10px; 
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
    background-color: transparent;
}

.nav-link .icon-wrapper { 
    font-size: 1.15rem; 
    margin-right: 14px; 
    width: 24px; 
    display: flex;
    justify-content: center;
    align-items: center;
    transition: color 0.2s;
}

.nav-link:hover { 
    color: #fff; 
    background-color: rgba(255,255,255,0.06); 
}

.nav-link.active { 
    background-color: var(--accent-color); 
    color: #fff; 
    font-weight: 600; 
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.nav-link.active .icon-wrapper {
    color: #fff;
}

/* Submenu container transition */
.submenu-container {
    overflow: hidden;
    transition: max-height 0.3s ease-in-out;
}

.submenu-toggle .arrow { 
    transition: transform 0.3s ease; 
    font-size: 0.8rem; 
}

.has-submenu.open .submenu-toggle .arrow { 
    transform: rotate(180deg); 
}

.has-submenu.open .submenu-toggle:not(.active) {
    background-color: rgba(255,255,255,0.03);
    color: #fff;
}

.sub-menu { 
    list-style: none;
    padding: 0.5rem 0 0.5rem 3rem; 
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.sub-link { 
    display: block; 
    color: var(--text-muted); 
    font-size: 0.9rem; 
    padding: 0.5rem 1rem; 
    border-radius: 8px;
    text-decoration: none; 
    transition: all 0.2s; 
}

.sub-link:hover { 
    color: #fff; 
    background-color: rgba(255,255,255,0.06);
}

.sub-link.active { 
    color: #fff; 
    font-weight: 600; 
    background-color: rgba(255,255,255,0.1);
}

/* Sidebar Footer */
.sidebar-footer { 
    padding: 1rem 1.5rem; 
    background-color: rgba(0,0,0,0.25); 
    border-top: 1px solid rgba(255,255,255,0.08);
    display: flex; 
    align-items: center; 
    justify-content: space-between; 
}

.user-profile { 
    display: flex; 
    align-items: center; 
    gap: 12px; 
}

.avatar { 
    width: 36px; 
    height: 36px; 
    background: linear-gradient(135deg, var(--accent-color), #1d4ed8); 
    color: white; 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    font-size: 1.1rem; 
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4);
}

.info .name { 
    font-size: 0.85rem; 
    font-weight: 600; 
    color: #fff; 
    line-height: 1.2; 
}

.info .role { 
    font-size: 0.7rem; 
    color: var(--text-muted); 
}

.btn-logout { 
    background: rgba(255,255,255,0.05); 
    border: none; 
    color: var(--text-muted); 
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer; 
    transition: all 0.2s; 
}

.btn-logout:hover { 
    color: #ef4444; 
    background: rgba(239, 68, 68, 0.1);
}

/* Main Wrapper */
.main-wrapper { 
    flex-grow: 1; 
    margin-left: var(--sidebar-width); 
    min-height: 100vh; 
    display: flex; 
    flex-direction: column; 
    transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
    background-color: var(--admin-bg); 
    width: calc(100% - var(--sidebar-width)); 
}

@media (min-width: 1024px) { 
    .sidebar.closed { transform: translateX(-100%); } 
    .main-wrapper.full-width { margin-left: 0; width: 100%; } 
}

/* Topbar */
.topbar { 
    height: var(--header-height); 
    background: rgba(255, 255, 255, 0.8); 
    backdrop-filter: blur(12px); 
    border-bottom: 1px solid rgba(0,0,0,0.05); 
    display: flex; 
    align-items: center; 
    justify-content: space-between; 
    padding: 0 2rem; 
    position: sticky; 
    top: 0; 
    z-index: 1040; 
}

.btn-toggle { 
    background: transparent; 
    border: none; 
    font-size: 1.5rem; 
    color: #475569; 
    cursor: pointer; 
    transition: color 0.2s; 
    padding: 0; 
    display: flex;
    align-items: center;
}

.btn-toggle:hover { color: var(--accent-color); }

.global-search { 
    position: relative; 
    width: 300px; 
    margin-left: 1.5rem; 
}

.search-input { 
    width: 100%; 
    background-color: #f1f5f9; 
    border: 1px solid transparent; 
    border-radius: 50px; 
    padding: 0.5rem 1rem 0.5rem 2.5rem; 
    font-size: 0.85rem; 
    color: #334155; 
    transition: all 0.2s; 
}

.search-input:focus { 
    background-color: #fff; 
    border-color: #cbd5e1; 
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); 
    outline: none; 
}

.search-icon { 
    position: absolute; 
    left: 14px; 
    top: 50%; 
    transform: translateY(-50%); 
    color: #94a3b8; 
    font-size: 0.9rem; 
}

.search-shortcut { 
    position: absolute; 
    right: 12px; 
    top: 50%; 
    transform: translateY(-50%); 
    background: #e2e8f0; 
    color: #64748b; 
    font-size: 0.7rem; 
    font-weight: 700; 
    padding: 2px 6px; 
    border-radius: 4px; 
}

.btn-icon { 
    position: relative; 
    background: #f1f5f9; 
    border: none; 
    color: #475569; 
    font-size: 1.1rem; 
    width: 38px; 
    height: 38px; 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    transition: all 0.2s; 
}

.btn-icon:hover { 
    background-color: #e2e8f0; 
    color: var(--accent-color); 
}

.badge-dot { 
    position: absolute; 
    top: 8px; 
    right: 10px; 
    width: 8px; 
    height: 8px; 
    background-color: #ef4444; 
    border-radius: 50%; 
    border: 2px solid #fff; 
}

.user-dropdown { 
    display: flex; 
    align-items: center; 
    cursor: pointer;
}

.avatar-sm { 
    width: 36px; 
    height: 36px; 
    background: linear-gradient(135deg, var(--accent-color), #1d4ed8); 
    color: #fff; 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    font-size: 1rem; 
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
}

.content-body { 
    padding: 2rem; 
    flex-grow: 1; 
}

.footer { 
    padding: 1.5rem 2rem; 
    text-align: center; 
    color: #94a3b8; 
    font-size: 0.85rem; 
}

@media (max-width: 1023.98px) {
    .sidebar { transform: translateX(-100%); }
    .sidebar:not(.closed) { transform: translateX(0); }
    .main-wrapper { margin-left: 0 !important; width: 100% !important; }
    .topbar { padding: 0 1rem; }
    .sidebar-backdrop { 
        position: fixed; 
        top: 0; left: 0; 
        width: 100vw; height: 100vh; 
        background: rgba(11, 17, 32, 0.4); 
        z-index: 1045; 
        backdrop-filter: blur(4px); 
        opacity: 0; 
        visibility: hidden; 
        transition: all 0.3s ease; 
    }
    .sidebar-backdrop.show { opacity: 1; visibility: visible; }
}

.custom-scroll::-webkit-scrollbar { width: 4px; }
.custom-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
.custom-scroll::-webkit-scrollbar-track { background: transparent; }
.custom-scroll:hover::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); }
</style>