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

            <h2 class="text-2xl font-bold text-gray-900">Terms and Conditions</h2>
            <p class="mt-2 text-sm text-gray-500">Last updated: August 2026</p>

            <div class="mt-6 space-y-5 text-sm leading-relaxed text-gray-600">
                <p>
                    Welcome to our e-invoicing platform. By creating an account
                    and using our services, you agree to these Terms and Conditions.
                </p>

                <div>
                    <h3 class="font-semibold text-gray-900">1. Free Trial</h3>
                    <p>
                        We provide a 7-day free trial period for new users.
                        During the trial period, you can explore our invoicing
                        features and determine if our service meets your business
                        requirements.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900">2. Account Registration</h3>
                    <p>
                        You must provide accurate and complete information when
                        creating an account. You are responsible for keeping your
                        login credentials secure.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900">3. Use of Service</h3>
                    <p>
                        Our platform provides tools for creating invoices,
                        managing customers, tracking payments, and organizing
                        business records. You agree not to misuse the service.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900">4. Subscription and Payments</h3>
                    <p>
                        After your free trial ends, continued access may require
                        a paid subscription. Subscription pricing and billing
                        details will be displayed before payment.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900">5. User Responsibilities</h3>
                    <p>
                        You are responsible for ensuring that all invoice details,
                        customer information, and business records entered into
                        the platform are accurate.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900">6. Account Suspension</h3>
                    <p>
                        We may suspend or terminate accounts that violate these
                        Terms and Conditions or misuse our services.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900">7. Changes to Terms</h3>
                    <p>
                        We may update these Terms and Conditions from time to time.
                        Continued use of the platform means you accept the updated
                        terms.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900">8. Contact Us</h3>
                    <p>
                        If you have questions regarding these Terms, please contact
                        our support team.
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
