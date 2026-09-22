<script setup lang="ts">
import { useAOS } from "@/composables/useAOS";

useAOS();
</script>

<template>
    <section id="about" class="bg-white py-20">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div class="relative" data-aos="zoom-in" data-aos-delay="200">
                    <div class="absolute inset-0 rounded-full bg-blue-100 opacity-40 blur-3xl"></div>
                    <img src="images/ac4-no-bg.webp" alt="About E-Invoicing" class="relative w-full rounded-3xl" data-aos="zoom-in" data-aos-delay="200" />
                </div>

                <div data-aos="fade-left" data-aos-duration="1000">
                    <span class="text-sm font-semibold text-blue-600">ABOUT US</span>

                    <h2 class="mt-4 text-4xl font-extrabold text-gray-900" data-aos="fade-up" data-aos-delay="200">
                        Simplifying Billing For Modern Businesses
                    </h2>

                    <p class="mt-5 leading-relaxed text-gray-600" data-aos="fade-up" data-aos-delay="300">
                        Our e-invoicing platform helps businesses create, manage, and track invoices faster. We provide secure digital billing solutions that reduce manual work and improve financial operations.
                    </p>

                    <div class="mt-8 grid gap-5 sm:grid-cols-2">
                        <div class="rounded-2xl bg-gray-50 p-5" data-aos="fade-up" data-aos-delay="400">
                            <h3 class="font-bold text-gray-900">Easy Management</h3>
                            <p class="mt-2 text-sm text-gray-500">Manage invoices and customers in one place.</p>
                        </div>

                        <div class="rounded-2xl bg-gray-50 p-5" data-aos="fade-up" data-aos-delay="500">
                            <h3 class="font-bold text-gray-900">Secure Platform</h3>
                            <p class="mt-2 text-sm text-gray-500">Your financial data stays protected.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
