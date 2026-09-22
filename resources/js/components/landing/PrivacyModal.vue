<script setup lang="ts">
defineProps<{ show: boolean }>();
const emit = defineEmits(["close"]);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white p-8 shadow-lg">
            <button type="button" @click="emit('close')" class="absolute top-4 right-5 text-3xl font-bold text-gray-400 hover:text-gray-700">
                &times;
            </button>

            <h2 class="text-2xl font-bold text-gray-900">Privacy Policy</h2>
            <p class="mt-2 text-sm text-gray-500">Last updated: August 2026</p>

            <div class="mt-6 space-y-5 text-sm leading-relaxed text-gray-600">
                <p>
                    We respect your privacy and are committed to protecting your
                    personal and business information when you use our
                    e-invoicing platform.
                </p>

                <div>
                    <h3 class="font-semibold text-gray-900">1. Information We Collect</h3>
                    <p>
                        We collect information needed to provide our services,
                        including your name, company name, email address, phone
                        number, account details, and business information.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900">2. How We Use Your Information</h3>
                    <p>
                        Your information is used to create your account, provide
                        e-invoicing features, manage subscriptions, improve our
                        platform, and communicate important updates.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900">3. Protection of Your Data</h3>
                    <p>
                        We implement appropriate security measures to help protect
                        your personal and business information from unauthorized
                        access, modification, or disclosure.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900">4. Business and Invoice Data</h3>
                    <p>
                        Invoice records, customer information, and business data
                        stored in your account are used only to provide the
                        requested services and improve your experience.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900">5. Sharing of Information</h3>
                    <p>
                        We do not sell your personal information. We may share
                        information only when required to provide services, comply
                        with legal requirements, or protect our platform.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900">6. Cookies and Analytics</h3>
                    <p>
                        We may use cookies and analytics technologies to improve
                        performance, understand usage patterns, and enhance the
                        user experience.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900">7. Your Rights</h3>
                    <p>
                        You may request access, correction, or deletion of your
                        personal information by contacting our support team.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900">8. Contact Us</h3>
                    <p>
                        If you have questions about this Privacy Policy or how your
                        information is handled, please contact our support team.
                    </p>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="button" @click="emit('close')" class="rounded-xl bg-blue-600 px-6 py-3 font-semibold text-white transition hover:bg-blue-700">
                    Close
                </button>
            </div>
        </div>
    </div>
</template>
