<script setup lang="ts">
import { usePage } from "@inertiajs/vue3";
import LandingHeader from "@/layouts/landing/LandingHeader.vue";

interface Country {
    id: number;
    code: string;
    name: string;
    phone_code: string | null;
}

const page = usePage<{
    countries: Country[];
}>();
</script>

<template>
    <div class="min-h-screen flex flex-col bg-gray-50">
        <LandingHeader :countries="page.props.countries" />

        <main>
            <slot />
        </main>
    </div>
</template>
