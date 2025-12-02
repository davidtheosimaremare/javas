<script setup>
import { Head, useForm } from '@inertiajs/vue3'

// --- ASSETS ---
// Gunakan logo yang sama dengan website utama
const logo = '/images/logo.png' 

// --- FORM LOGIC ---
const form = useForm({
    email: '',
    password: '',
    remember: false
})

const submit = () => {
    form.post('/admin/login', {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Admin Login - JBB Group" />

    <div class="admin-login-page">
        
        <div class="login-container">
            
            <div class="text-center mb-4">
                <img :src="logo" alt="JBB Logo" class="login-logo mb-3">
                <h4 class="fw-bold text-blue">Admin Portal</h4>
                <p class="text-muted small">Silakan masuk untuk mengelola website</p>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    
                    <div v-if="form.errors.email" class="alert alert-danger py-2 d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <small>{{ form.errors.email }}</small>
                    </div>

                    <form @submit.prevent="submit">
                        
                        <div class="form-floating mb-3">
                            <input 
                                v-model="form.email" 
                                type="email" 
                                class="form-control" 
                                id="emailInput" 
                                placeholder="name@example.com"
                                :class="{ 'is-invalid': form.errors.email }"
                                required 
                                autofocus
                            >
                            <label for="emailInput">Email Address</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input 
                                v-model="form.password" 
                                type="password" 
                                class="form-control" 
                                id="passwordInput" 
                                placeholder="Password"
                                required
                            >
                            <label for="passwordInput">Password</label>
                        </div>

                        <div class="form-check mb-4">
                            <input 
                                v-model="form.remember" 
                                class="form-check-input" 
                                type="checkbox" 
                                id="rememberCheck"
                            >
                            <label class="form-check-label text-muted small" for="rememberCheck">
                                Ingat saya di perangkat ini
                            </label>
                        </div>

                        <button 
                            type="submit" 
                            class="btn btn-primary-admin w-100 py-3 fw-bold" 
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                            {{ form.processing ? 'Sedang Masuk...' : 'LOGIN' }}
                        </button>

                    </form>
                </div>
                <div class="card-footer bg-light text-center py-3">
                    <small class="text-muted text-copyright">&copy; 2025 PT JBB Javas Berkah Bistari</small>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="/" class="text-white text-decoration-none small opacity-75 hover-opacity">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Website Utama
                </a>
            </div>

        </div>

    </div>
</template>

<style scoped>
/* --- PAGE BACKGROUND --- */
.admin-login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #002b49 0%, #001f35 100%);
    position: relative;
    padding: 20px;
}

/* Background Pattern (Optional - Biar tidak polos) */
.admin-login-page::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}

/* --- CONTAINER --- */
.login-container {
    width: 100%;
    max-width: 450px;
    position: relative;
    z-index: 2;
}

/* --- LOGO & TEXT --- */
.login-logo {
    height: 60px;
    width: auto;
    filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));
}
.text-blue {
    color: #fff; /* Judul putih karena background gelap */
}
.text-muted {
    color: #aab2bd !important;
}

/* --- CARD --- */
.card {
    background: rgba(255, 255, 255, 0.95); /* Sedikit transparan */
    backdrop-filter: blur(10px);
}

/* --- FORM ELEMENTS --- */
.form-control {
    border: 1px solid #dee2e6;
    background-color: #f8f9fa;
}
.form-control:focus {
    background-color: #fff;
    border-color: #002b49;
    box-shadow: 0 0 0 3px rgba(0, 43, 73, 0.15);
}
.form-floating label {
    color: #6c757d;
}

/* --- BUTTON --- */
.btn-primary-admin {
    background-color: #002b49;
    color: #fff;
    border: none;
    transition: all 0.3s;
}
.btn-primary-admin:hover {
    background-color: #e63946; /* Aksen merah saat hover */
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(230, 57, 70, 0.3);
}
.btn-primary-admin:disabled {
    background-color: #ccc;
    transform: none;
}

/* --- FOOTER --- */
.text-copyright {
    font-size: 0.75rem;
}
.hover-opacity:hover {
    opacity: 1 !important;
    text-decoration: underline !important;
}
</style>