<script setup lang="ts">
import { Mail, Phone, MapPin } from '@lucide/vue';
import { useAOS } from "@/composables/useAOS";

useAOS();
</script>

<template>
    <section id="contact" class="bg-gray-50 py-20">
        <div class="mx-auto max-w-7xl px-6">
            <div class="grid gap-12 lg:grid-cols-2">
                <div data-aos="fade-right" data-aos-duration="1000">
                    <h2 class="text-4xl font-extrabold text-gray-900">Contact Us</h2>
                    <p class="mt-4 text-gray-600" data-aos="fade-up" data-aos-delay="200">
                        Have questions? Our team is ready to help.
                    </p>

                    <div class="mt-8 space-y-4 text-gray-700">
                        <p data-aos="fade-up" data-aos-delay="300">
                            <Mail class="mr-2 inline-block" /> support@einvoicing.com
                        </p>
                        <p data-aos="fade-up" data-aos-delay="400">
                            <Phone class="mr-2 inline-block" /> +63 900 000 0000
                        </p>
                        <p data-aos="fade-up" data-aos-delay="500">
                            <MapPin class="mr-2 inline-block" /> Philippines
                        </p>
                    </div>
                </div>

                <form class="space-y-5 rounded-3xl bg-white p-8 text-gray-700 shadow-xl" data-aos="fade-left" data-aos-duration="1000">
                    <h2 class="text-2xl font-bold text-gray-900" data-aos="fade-up">Send us a message</h2>

                    <div data-aos="fade-up" data-aos-delay="200">
                        <label class="block font-semibold text-gray-700">Name</label>
                        <input class="w-full rounded-xl border-2 border-gray-300 p-2" placeholder="Enter Name" />
                    </div>

                    <div data-aos="fade-up" data-aos-delay="300">
                        <label class="block font-semibold text-gray-700">Email</label>
                        <input class="w-full rounded-xl border-2 border-gray-300 p-2" placeholder="Enter Email" />
                    </div>

                    <div data-aos="fade-up" data-aos-delay="400">
                        <label class="block font-semibold text-gray-700">Message</label>
                        <textarea class="w-full rounded-xl border-2 border-gray-300 p-2" rows="5" placeholder="Message Here" />
                    </div>

                    <button class="w-full rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 py-3 font-semibold text-white" data-aos="zoom-in" data-aos-delay="500">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>
