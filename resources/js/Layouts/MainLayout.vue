<script setup>
import Header from '../Components/Partials/Header.vue';
import Footer from '../Components/Partials/Footer.vue';
</script>

<template>
    <div class="d-flex flex-column min-vh-100">
        <Header />

        <main class="grow">
            <slot />
        </main>

        <Footer />
    </div>
</template>