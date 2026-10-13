<script setup lang="ts">
import { computed, ref } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Eye, EyeOff, X } from "@lucide/vue";
import InputError from "@/components/InputError.vue";
import TermsModal from "@/components/landing/TermsModal.vue";
import PrivacyModal from "@/components/landing/PrivacyModal.vue";
import type { TrialRegistrationData } from "@/types/forms/RegistrationData";

interface Country { id: number; code: string; name: string; phone_code: string | null }

const props = defineProps<{ countries: Country[] }>();

const showPassword = ref(false);
const showTermsModal = ref(false);
const showPrivacyModal = ref(false);

const form = useForm<TrialRegistrationData>({
    name: "", email: "", password: "", tin: "", registered_name: "", trade_name: "",
    branch_code: "", address_line_1: "", address_line_2: "", barangay: "", city: "",
    province: "", postal_code: "", country_id: "", company_email: "", phone: "", terms: false,
});

const selectedCountry = computed(() => props.countries?.find((c) => c.id === Number(form.country_id)));

// Digits only, no leading zeros (0917... -> 917...), prefixed with country code
const buildPhone = (): string => {
    const local = String(form.phone ?? "").replace(/\D/g, "").replace(/^0+/, "");
    const code = selectedCountry.value?.phone_code?.replace(/\D/g, "");
    return code ? `+${code}${local}` : local;
};

const submit = () => {
    form.transform((data) => ({ ...data, phone: buildPhone() })).post("/register", {
        preserveScroll: true,
        onError: () => form.reset("password"),
    });
};

const inputClass = "h-9 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20";
const labelClass = "mb-1.5 block text-xs font-medium text-gray-700";
const titleClass = "text-xs font-semibold uppercase tracking-wide text-gray-900";
const descClass = "mt-0.5 text-[11px] text-gray-500";
</script>

<template>
    <Head title="Create an account" />

    <div class="fixed inset-0 overflow-y-auto bg-gray-100">
        <div class="mx-auto w-full max-w-6xl bg-white sm:my-6 sm:rounded-xl sm:shadow-lg">
            <!-- Header -->
            <div class="flex items-start justify-between border-b border-gray-200 px-6 py-4 sm:px-8">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Start Your Free Trial</h1>
                    <p class="mt-0.5 text-xs text-gray-500">Get 7 days free access. No credit card required.</p>
                </div>
                <Link href="/" aria-label="Close" class="rounded-md p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"><X class="h-4 w-4" /></Link>
            </div>

            <form @submit.prevent="submit" class="w-full px-6 py-6 sm:px-8">
                <!-- Account Information -->
                <section class="mb-5">
                    <div class="mb-3"><h2 :class="titleClass">Account Information</h2><p :class="descClass">Create your administrator account.</p></div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label for="name" :class="labelClass">Full Name <span class="text-red-500">*</span></label>
                            <input id="name" v-model="form.name" type="text" autocomplete="name" placeholder="Juan Dela Cruz" :class="inputClass" />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div>
                            <label for="email" :class="labelClass">Email Address <span class="text-red-500">*</span></label>
                            <input id="email" v-model="form.email" type="email" autocomplete="email" placeholder="email@company.com" :class="inputClass" />
                            <InputError :message="form.errors.email" />
                        </div>
                        <div>
                            <label for="password" :class="labelClass">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" autocomplete="new-password" placeholder="Enter password" :class="[inputClass, 'pr-10']" />
                                <button type="button" :aria-label="showPassword ? 'Hide password' : 'Show password'" @click="showPassword = !showPassword" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <Eye v-if="!showPassword" class="h-4 w-4" />
                                    <EyeOff v-else class="h-4 w-4" />
                                </button>
                            </div>
                            <InputError :message="form.errors.password" />
                        </div>
                    </div>
                </section>

                <!-- Business Information -->
                <section class="mb-5">
                    <div class="mb-3"><h2 :class="titleClass">Business Information</h2><p :class="descClass">Provide your registered business information.</p></div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label for="registered_name" :class="labelClass">Registered Company Name <span class="text-red-500">*</span></label>
                            <input id="registered_name" v-model="form.registered_name" type="text" autocomplete="organization" placeholder="ABC Corporation" :class="inputClass" />
                            <InputError :message="form.errors.registered_name" />
                        </div>
                        <div>
                            <label for="trade_name" :class="labelClass">Trade Name <span class="text-red-500">*</span></label>
                            <input id="trade_name" v-model="form.trade_name" type="text" placeholder="ABC" :class="inputClass" />
                            <InputError :message="form.errors.trade_name" />
                        </div>
                        <div>
                            <label for="tin" :class="labelClass">TIN <span class="text-red-500">*</span></label>
                            <input id="tin" v-model="form.tin" type="text" inputmode="numeric" placeholder="000-000-000-000" :class="inputClass" />
                            <InputError :message="form.errors.tin" />
                        </div>
                        <div>
                            <label for="branch_code" :class="labelClass">Branch Code <span class="text-red-500">*</span></label>
                            <input id="branch_code" v-model="form.branch_code" type="text" inputmode="numeric" placeholder="000" :class="inputClass" />
                            <InputError :message="form.errors.branch_code" />
                        </div>
                    </div>
                </section>

                <!-- Business Address -->
                <section class="mb-5">
                    <div class="mb-3"><h2 :class="titleClass">Business Address</h2><p :class="descClass">Enter your registered business address.</p></div>
                    <div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="address_line_1" :class="labelClass">Address Line 1 <span class="text-red-500">*</span></label>
                            <input id="address_line_1" v-model="form.address_line_1" type="text" autocomplete="address-line1" placeholder="Building / Street" :class="inputClass" />
                            <InputError :message="form.errors.address_line_1" />
                        </div>
                        <div>
                            <label for="address_line_2" :class="labelClass">Address Line 2 <span class="ml-1 text-[10px] font-normal text-gray-400">Optional</span></label>
                            <input id="address_line_2" v-model="form.address_line_2" type="text" autocomplete="address-line2" placeholder="Suite / Unit / Floor" :class="inputClass" />
                            <InputError :message="form.errors.address_line_2" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
                        <div>
                            <label for="country_id" :class="labelClass">Country <span class="text-red-500">*</span></label>
                            <select id="country_id" v-model="form.country_id" autocomplete="country" :class="inputClass">
                                <option value="">Select country</option>
                                <option v-for="country in props.countries" :key="country.id" :value="country.id">{{ country.name }} ({{ country.code }})</option>
                            </select>
                            <InputError :message="form.errors.country_id" />
                        </div>
                        <div>
                            <label for="province" :class="labelClass">Province <span class="text-red-500">*</span></label>
                            <input id="province" v-model="form.province" type="text" autocomplete="address-level1" placeholder="Province" :class="inputClass" />
                            <InputError :message="form.errors.province" />
                        </div>
                        <div>
                            <label for="city" :class="labelClass">City <span class="text-red-500">*</span></label>
                            <input id="city" v-model="form.city" type="text" autocomplete="address-level2" placeholder="City" :class="inputClass" />
                            <InputError :message="form.errors.city" />
                        </div>
                        <div>
                            <label for="barangay" :class="labelClass">Barangay <span class="text-red-500">*</span></label>
                            <input id="barangay" v-model="form.barangay" type="text" placeholder="Barangay" :class="inputClass" />
                            <InputError :message="form.errors.barangay" />
                        </div>
                        <div>
                            <label for="postal_code" :class="labelClass">Postal Code <span class="text-red-500">*</span></label>
                            <input id="postal_code" v-model="form.postal_code" type="text" inputmode="numeric" autocomplete="postal-code" placeholder="1100" :class="inputClass" />
                            <InputError :message="form.errors.postal_code" />
                        </div>
                    </div>
                </section>

                <!-- Business Contact -->
                <section class="mb-2">
                    <div class="mb-3"><h2 :class="titleClass">Business Contact</h2><p :class="descClass">Contact information for your business.</p></div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="company_email" :class="labelClass">Business Email <span class="text-red-500">*</span></label>
                            <input id="company_email" v-model="form.company_email" type="email" placeholder="contact@company.com" :class="inputClass" />
                            <InputError :message="form.errors.company_email" />
                        </div>
                        <div>
                            <label for="phone" :class="labelClass">Phone Number <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <div class="flex h-9 w-28 shrink-0 items-center rounded-md border border-gray-300 bg-gray-50 px-3 text-xs text-gray-700">{{ selectedCountry?.phone_code ?? "Code" }}</div>
                                <input id="phone" v-model="form.phone" type="tel" inputmode="tel" autocomplete="tel-national" placeholder="9171234567" :class="[inputClass, 'min-w-0 flex-1']" />
                            </div>
                            <InputError :message="form.errors.phone" />
                        </div>
                    </div>
                </section>

                <!-- Terms & Submit -->
                <div class="mt-5 border-t border-gray-200 pt-4">
                    <div class="flex items-start gap-2">
                        <input id="terms" v-model="form.terms" type="checkbox" required class="mt-0.5 h-3.5 w-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        <p class="text-xs leading-5 text-gray-600">
                            I agree to the
                            <button type="button" @click="showTermsModal = true" class="font-medium text-blue-600 hover:underline">Terms and Conditions</button>
                            and
                            <button type="button" @click="showPrivacyModal = true" class="font-medium text-blue-600 hover:underline">Privacy Policy</button>.
                        </p>
                    </div>
                    <InputError :message="form.errors.terms" />

                    <div class="mt-4 flex justify-end gap-2">
                        <Link href="/" class="rounded-md border border-gray-300 bg-white px-5 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300">Cancel</Link>
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-blue-600 px-6 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                            {{ form.processing ? "Creating Account..." : "Create Account" }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <TermsModal :show="showTermsModal" @close="showTermsModal = false" />
    <PrivacyModal :show="showPrivacyModal" @close="showPrivacyModal = false" />
</template>
