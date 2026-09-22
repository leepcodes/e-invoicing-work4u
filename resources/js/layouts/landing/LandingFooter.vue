<script setup lang="ts">
import { MapPin, Phone, Mail } from '@lucide/vue';
import { useAOS } from "@/composables/useAOS";

useAOS();
</script>

<template>
    <footer class="bg-white border-t border-gray-200 mt-20">
        <div class="max-w-7xl mx-auto px-6 py-14">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <div data-aos="fade-up" data-aos-delay="100">
                    <a href="#home" class="flex items-center gap-3 mb-4">
                        <img src="images/logo/example.logo.no-bg.webp" alt="E-Invoicing Logo" class="w-10 h-10 rounded-lg" />
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">E-Invoicing</h2>
                            <p class="text-sm text-gray-500">Smart Billing Solution</p>
                        </div>
                    </a>

                    <p class="text-gray-600 leading-7">
                        Simplify invoicing, billing, and payment tracking with
                        our modern web-based e-invoicing platform built for
                        businesses of all sizes.
                    </p>
                </div>

                <div data-aos="fade-up" data-aos-delay="300">
                    <h3 class="text-gray-900 font-semibold mb-4">Company</h3>
                    <ul class="space-y-3">
                        <li><a href="#home" class="text-gray-600 hover:text-blue-600 transition">Home</a></li>
                        <li><a href="#about" class="text-gray-600 hover:text-blue-600 transition">About Us</a></li>
                        <li><a href="#feature" class="text-gray-600 hover:text-blue-600 transition">Features</a></li>
                        <li><a href="#pricing" class="text-gray-600 hover:text-blue-600 transition">Pricing</a></li>
                        <li><a href="#contact" class="text-gray-600 hover:text-blue-600 transition">Contact</a></li>
                    </ul>
                </div>

                <div data-aos="fade-up" data-aos-delay="300">
                    <h3 class="text-gray-900 font-semibold mb-4">Resources</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-gray-600 hover:text-blue-600 transition">Help Center</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-blue-600 transition">Documentation</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-blue-600 transition">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-600 hover:text-blue-600 transition">Terms & Conditions</a></li>
                    </ul>
                </div>

                <div data-aos="fade-up" data-aos-delay="400">
                    <h3 class="text-gray-900 font-semibold mb-4">Contact</h3>
                    <ul class="space-y-4 text-gray-600">
                        <li class="flex items-center gap-3">
                            <MapPin class="w-5 h-5 text-black" />
                            <span>123 Business Ave, Suite 100</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <Phone class="w-5 h-5 text-black" />
                            <span>+63 912 345 6789</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <Mail class="w-5 h-5 text-black" />
                            <span>support@einvoicing.com</span>
                        </li>
                    </ul>

                    <div class="flex gap-4 mt-6" data-aos="zoom-in" data-aos-delay="500">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-blue-600 hover:text-white transition flex items-center justify-center">
                            <img src="icons/fb.svg" alt="Facebook" class="w-5 h-5" />
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-blue-600 hover:text-white transition flex items-center justify-center">
                            <img src="icons/twt.svg" alt="Twitter" class="w-5 h-5" />
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-blue-600 hover:text-white transition flex items-center justify-center">
                            <img src="icons/linkedin.svg" alt="LinkedIn" class="w-5 h-5" />
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-blue-600 hover:text-white transition flex items-center justify-center">
                            <img src="icons/instagram.svg" alt="Instagram" class="w-5 h-5" />
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 mt-12 pt-6 flex flex-col md:flex-row justify-center items-center" data-aos="fade-up">
                <p class="text-sm text-gray-500">
                    © {{ new Date().getFullYear() }} E-Invoicing. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</template>
