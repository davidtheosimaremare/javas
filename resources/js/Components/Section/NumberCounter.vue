<script setup>
import { ref, onMounted, watch } from 'vue';

const props = defineProps({
    target: { type: Number, required: true },
    duration: { type: Number, default: 2000 } // Durasi animasi ms
});

const currentNumber = ref(0);
const counterRef = ref(null);

const startAnimation = () => {
    let startTimestamp = null;
    const startValue = 0;
    
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / props.duration, 1);
        
        // Easing function (easeOutExpo) agar gerakan halus
        const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
        
        currentNumber.value = Math.floor(easeProgress * (props.target - startValue) + startValue);
        
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    
    window.requestAnimationFrame(step);
};

// Gunakan Intersection Observer agar animasi baru mulai saat discroll
onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
            startAnimation();
            observer.disconnect();
        }
    });
    
    if (counterRef.value) observer.observe(counterRef.value);
});
</script>

<template>
    <span ref="counterRef">{{ currentNumber }}</span>
</template>