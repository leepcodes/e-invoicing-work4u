<script setup lang="ts">
import { ref } from "vue";
import { useAOS } from "@/composables/useAOS";
import TrialRegistrationModal from "@/components/landing/TrialRegistrationModal.vue";

const showTrialModal = ref(false);
const openTrialModal = () => showTrialModal.value = true;
const closeTrialModal = () => showTrialModal.value = false;

useAOS();
</script>

<template>
    <section id="home" class="relative overflow-hidden bg-white">
        <div class="mx-auto max-w-7xl px-6 py-20 lg:py-28">
            <div class="grid items-center gap-14 lg:grid-cols-2">
                <div class="text-center lg:text-left" data-aos="fade-right">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-600" data-aos="fade-down" data-aos-delay="100">
                        <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                        E-invoicing is now live! Check out our new features.
                    </div>

                    <h1 class="text-4xl font-extrabold leading-tight tracking-tight text-gray-900 sm:text-5xl lg:text-6xl" data-aos="fade-up" data-aos-delay="200">
                        Simplify Your
                        <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Invoice Management</span>
                    </h1>

                    <p class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-gray-600 lg:mx-0" data-aos="fade-up" data-aos-delay="300">
                        Create invoices, manage customers, track payments, and automate your billing workflow with a powerful e-invoicing solution built for modern businesses.
                    </p>

                    <div class="mt-8 flex flex-col justify-center gap-4 sm:flex-row lg:justify-start" data-aos="fade-up" data-aos-delay="400">
                        <button @click="openTrialModal" class="rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-3.5 font-semibold text-white shadow-lg shadow-blue-500/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                            Start Free Trial
                        </button>

                        <a href="/#about" class="rounded-xl border border-gray-200 px-8 py-3.5 font-semibold text-gray-700 transition hover:bg-gray-50">
                            Learn More
                        </a>
                    </div>

                    <div class="mx-auto mt-10 grid max-w-lg grid-cols-3 gap-5 lg:mx-0" data-aos="zoom-in" data-aos-delay="500">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">99%</h3>
                            <p class="text-sm text-gray-500">Accuracy</p>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">10K+</h3>
                            <p class="text-sm text-gray-500">Invoices</p>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">24/7</h3>
                            <p class="text-sm text-gray-500">Support</p>
                        </div>
                    </div>
                </div>

                <div class="relative flex justify-center lg:justify-end" data-aos="fade-left">
                    <div class="absolute inset-0 rounded-full bg-blue-100 opacity-40 blur-3xl"></div>

                    <img src="/images/ac3-no-bg.webp" alt="E-Invoicing Dashboard" class="relative w-full max-w-lg rounded-3xl lg:max-w-xl" />

                    <div class="absolute -bottom-5 -left-5 hidden rounded-2xl border border-gray-100 bg-white px-5 py-4 shadow-xl sm:block" data-aos="zoom-in" data-aos-delay="700">
                        <p class="text-sm text-gray-500">Payments Processed</p>
                        <h3 class="text-2xl font-bold text-blue-600">₱500K+</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <TrialRegistrationModal :show="showTrialModal" @close="closeTrialModal" />
</template>
