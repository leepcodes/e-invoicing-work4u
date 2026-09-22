<script setup lang="ts">
import { ref } from "vue";
import { useAOS } from "@/composables/useAOS";
import TrialRegistrationModal from "@/components/landing/TrialRegistrationModal.vue";

useAOS();

const showTrialModal = ref(false);
const openTrialModal = () => showTrialModal.value = true;
const closeTrialModal = () => showTrialModal.value = false;
</script>

<template>
    <header class="sticky top-0 z-50 border-b border-gray-100 bg-white/90 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-20">
                <a href="#home" class="flex items-center gap-3 group" data-aos="fade-right" data-aos-duration="800">
                    <img src="images/logo/example.logo.no-bg.webp" alt="E-Invoicing Logo" class="w-11 h-11 rounded-xl shadow-sm group-hover:scale-105 transition duration-300" />
                    <div class="leading-tight">
                        <h1 class="text-xl font-extrabold tracking-tight text-gray-900">E-Invoicing</h1>
                        <p class="text-xs font-medium text-gray-500">Smart Billing Solution</p>
                    </div>
                </a>

                <nav class="hidden md:flex items-center gap-9" data-aos="fade-down" data-aos-duration="800" data-aos-delay="100">
                    <a href="#home" class="relative text-sm font-semibold text-gray-600 hover:text-blue-600 transition group">
                        Home
                        <span class="absolute left-0 -bottom-2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all"></span>
                    </a>
                    <a href="#about" class="relative text-sm font-semibold text-gray-600 hover:text-blue-600 transition group">
                        About
                        <span class="absolute left-0 -bottom-2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all"></span>
                    </a>
                    <a href="#feature" class="relative text-sm font-semibold text-gray-600 hover:text-blue-600 transition group">
                        Features
                        <span class="absolute left-0 -bottom-2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all"></span>
                    </a>
                    <a href="#pricing" class="relative text-sm font-semibold text-gray-600 hover:text-blue-600 transition group">
                        Pricing
                        <span class="absolute left-0 -bottom-2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all"></span>
                    </a>
                    <a href="#contact" class="relative text-sm font-semibold text-gray-600 hover:text-blue-600 transition group">
                        Contact
                        <span class="absolute left-0 -bottom-2 w-0 h-0.5 bg-blue-600 group-hover:w-full transition-all"></span>
                    </a>
                </nav>

                <div class="flex items-center gap-3" data-aos="fade-left" data-aos-duration="800" data-aos-delay="200">
                    <a href="/login" class="hidden sm:block px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:text-blue-600 hover:bg-blue-50 transition">
                        Login
                    </a>

                    <button
                        @click="openTrialModal"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-lg shadow-blue-500/25 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300"
                    >
                        Get Started
                    </button>
                </div>
            </div>
        </div>
    </header>

    <TrialRegistrationModal :show="showTrialModal" @close="closeTrialModal" />
</template>
