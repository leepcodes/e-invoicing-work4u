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
    <section class="bg-white px-5 py-20">
        <div
            class="mx-auto max-w-7xl rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-16 text-center shadow-lg"
            data-aos="fade-up"
        >
            <h2 class="text-3xl font-extrabold text-white md:text-4xl">
                Ready to Simplify Your Invoicing?
            </h2>

            <p class="mx-auto mt-4 max-w-2xl text-sm leading-6 text-blue-100 md:text-base">
                Manage your invoices, customers, and business documents in one
                simple platform. Start your free trial today.
            </p>

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <button
                    type="button"
                    @click="openTrialModal"
                    class="rounded-md bg-white px-7 py-3 text-sm font-semibold text-blue-600 shadow-sm transition hover:bg-gray-100"
                >
                    Start Free Trial
                </button>

                <a
                    href="/#about"
                    class="rounded-md border border-blue-300 px-7 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    Learn More
                </a>
            </div>

            <p class="mt-5 text-xs text-blue-100">
                7 days free · No credit card required
            </p>
        </div>
    </section>

    <TrialRegistrationModal
        :show="showTrialModal"
        @close="closeTrialModal"
    />
</template>
