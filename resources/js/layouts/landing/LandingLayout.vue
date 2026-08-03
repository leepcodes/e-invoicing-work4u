<script setup lang="ts">
import LandingHeader from '@/layouts/landing/LandingHeader.vue';
import LandingFooter from '@/layouts/landing/LandingFooter.vue';
</script>

<template>

    <div class="min-h-screen flex flex-col bg-gray-50">

        <LandingHeader />
        <main>
            <slot />
        </main>
        <LandingFooter />

    </div>

</template>
