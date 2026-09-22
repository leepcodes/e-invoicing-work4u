<script setup lang="ts">
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Eye, EyeOff } from "@lucide/vue";
import InputError from "@/components/InputError.vue";
import TermsModal from "@/components/landing/TermsModal.vue";
import PrivacyModal from "@/components/landing/PrivacyModal.vue";
import type { TrialRegistrationData } from "@/types/forms/RegistrationData";

defineProps<{ show: boolean }>();
const emit = defineEmits<{ close: [] }>();

const showPassword = ref(false);
const showTermsModal = ref(false);
const showPrivacyModal = ref(false);

const openTermsModal = () => showTermsModal.value = true;
const closeTermsModal = () => showTermsModal.value = false;
const openPrivacyModal = () => showPrivacyModal.value = true;
const closePrivacyModal = () => showPrivacyModal.value = false;

const form = useForm<TrialRegistrationData>({
    name: "",
    email: "",
    password: "",
    tin: "",
    registered_name: "",
    trade_name: "",
    branch_code: "",
    address_line_1: "",
    address_line_2: "",
    barangay: "",
    city: "",
    province: "",
    postal_code: "",
    country_code: "",
    company_email: "",
    phone: "",
    terms: false,
});

const submit = () => {
    form.post("/register", {
        preserveScroll: true,
        onSuccess: () => {
            console.log("Account created");
            emit("close");
        },
        onError: (errors) => console.log(errors),
    });
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-4">
        <div class="max-h-[94vh] w-full max-w-6xl overflow-y-auto rounded-xl bg-white shadow-2xl">
            <div class="flex items-start justify-between border-b border-gray-200 px-6 py-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Start Your Free Trial</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Get 7 days free access. No credit card required.</p>
                </div>

                <button type="button" @click="emit('close')" class="flex h-8 w-8 items-center justify-center rounded-lg text-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-700">
                    ×
                </button>
            </div>

            <form @submit.prevent="submit" class="px-6 py-5">
                <section class="mb-5">
                    <div class="mb-3">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-900">Account Information</h3>
                        <p class="mt-0.5 text-[11px] text-gray-500">Create your administrator account.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Full Name <span class="text-red-500">*</span></label>
                            <input v-model="form.name" type="text" placeholder="Juan Dela Cruz" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label>
                            <input v-model="form.email" type="email" placeholder="email@company.com" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input v-model="form.password" :type="showPassword ? 'text' : 'password'" placeholder="Enter password" class="h-9 w-full rounded-md border border-gray-300 px-3 pr-10 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                                <button type="button" @click="showPassword = !showPassword" class="absolute right-2.5 top-1/2 -translate-y-1/2">
                                    <Eye v-if="!showPassword" class="h-4 w-4 text-gray-400" />
                                    <EyeOff v-else class="h-4 w-4 text-gray-400" />
                                </button>
                            </div>
                            <InputError :message="form.errors.password" />
                        </div>
                    </div>
                </section>

                <section class="mb-5">
                    <div class="mb-3">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-900">Business Information</h3>
                        <p class="mt-0.5 text-[11px] text-gray-500">Provide your registered business information.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Registered Company Name <span class="text-red-500">*</span></label>
                            <input v-model="form.registered_name" type="text" placeholder="ABC Corporation" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.registered_name" />
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Trade Name</label>
                            <input v-model="form.trade_name" type="text" placeholder="ABC" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.trade_name" />
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">TIN</label>
                            <input v-model="form.tin" type="text" placeholder="000-000-000-000" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.tin" />
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Branch Code</label>
                            <input v-model="form.branch_code" type="text" placeholder="000" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.branch_code" />
                        </div>
                    </div>
                </section>

                <section class="mb-5">
                    <div class="mb-3">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-900">Business Address</h3>
                        <p class="mt-0.5 text-[11px] text-gray-500">Enter your registered business address.</p>
                    </div>

                    <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Address Line 1 <span class="text-red-500">*</span></label>
                            <input v-model="form.address_line_1" type="text" placeholder="Building / Street" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.address_line_1" />
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Address Line 2 <span class="ml-1 text-[10px] font-normal text-gray-400">Optional</span></label>
                            <input v-model="form.address_line_2" type="text" placeholder="Suite / Unit / Floor" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.address_line_2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Country</label>
                            <select v-model="form.country_code" class="h-9 w-full rounded-md border border-gray-300 bg-white px-3 text-xs outline-none transition hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                <option value="">Select country</option>
                                <option value="PH">Philippines</option>
                                <option value="US">United States</option>
                                <option value="SG">Singapore</option>
                                <option value="MY">Malaysia</option>
                            </select>
                            <InputError :message="form.errors.country_code" />
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Province</label>
                            <input v-model="form.province" type="text" placeholder="Province" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.province" />
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">City</label>
                            <input v-model="form.city" type="text" placeholder="City" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.city" />
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Barangay</label>
                            <input v-model="form.barangay" type="text" placeholder="Barangay" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.barangay" />
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Postal Code</label>
                            <input v-model="form.postal_code" type="text" placeholder="1100" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.postal_code" />
                        </div>
                    </div>
                </section>

                <section class="mb-2">
                    <div class="mb-3">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-900">Business Contact</h3>
                        <p class="mt-0.5 text-[11px] text-gray-500">Contact information for your business.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Business Email</label>
                            <input v-model="form.company_email" type="email" placeholder="contact@company.com" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.company_email" />
                        </div>

                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Phone Number</label>
                            <input v-model="form.phone" type="text" placeholder="+63 9XX XXX XXXX" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.phone" />
                        </div>
                    </div>
                </section>

                <div class="mt-5 border-t border-gray-200 pt-4">
                    <div class="flex items-start gap-2">
                        <input v-model="form.terms" type="checkbox" required class="mt-0.5 h-3.5 w-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />

                        <p class="text-xs leading-5 text-gray-600">
                            I agree to the
                            <button type="button" @click="openTermsModal" class="font-medium text-blue-600 hover:underline">Terms and Conditions</button>
                            and
                            <button type="button" @click="openPrivacyModal" class="font-medium text-blue-600 hover:underline">Privacy Policy</button>.
                        </p>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="emit('close')" class="rounded-md border border-gray-300 bg-white px-5 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-50">
                            Cancel
                        </button>

                        <button type="submit" :disabled="form.processing" class="rounded-md bg-blue-600 px-6 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                            {{ form.processing ? "Creating Account..." : "Create Account" }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <TermsModal :show="showTermsModal" @close="closeTermsModal" />
    <PrivacyModal :show="showPrivacyModal" @close="closePrivacyModal" />
</template>
