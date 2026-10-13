<script setup lang="ts">
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Eye, EyeOff } from "@lucide/vue";
import InputError from "@/components/InputError.vue";
import TermsModal from "@/components/landing/TermsModal.vue";
import PrivacyModal from "@/components/landing/PrivacyModal.vue";

interface Country {
    id: number;
    code: string;
    name: string;
    phone_code: string | null;
}

interface TrialRegistrationData {
    name: string;
    email: string;
    password: string;
    tin: string;
    registered_name: string;
    trade_name: string;
    branch_code: string;
    address_line_1: string;
    address_line_2: string;
    barangay: string;
    city: string;
    province: string;
    postal_code: string;
    country_id: string;
    company_email: string;
    phone: string;
    terms: boolean;
}

const props = defineProps<{ show: boolean; countries: Country[] }>();
const emit = defineEmits<{ close: [] }>();

const showPassword = ref(false);
const showTermsModal = ref(false);
const showPrivacyModal = ref(false);

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
    country_id: "",
    company_email: "",
    phone: "",
    terms: false,
});

const selectedCountry = () =>
    props.countries.find(
        (country) => country.id === Number(form.country_id),
    );

const submit = () => {
    try {
        const country = selectedCountry();

        const phone = country?.phone_code
            ? `${country.phone_code}${form.phone}`
            : form.phone;

        console.log("Submitting registration data:", {
            ...form.data(),
            phone,
        });

        form.transform((data) => ({
            ...data,
            phone,
        })).post("/register", {
            preserveScroll: true,

            onSuccess: (page) => {
                console.log("Registration successful:", page);
                alert("Registration successful! Data was saved.");
                emit("close");
            },

            onError: (errors) => {
                console.error("Registration failed:", errors);
                alert("Registration failed. Please check the form errors.");
            },

            onFinish: () => {
                console.log("Registration request finished.");
            },
        });
    } catch (error) {
        console.error("Unexpected registration error:", error);
        alert("An unexpected error occurred. Check the browser console.");
    }
};

</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-4">
        <div class="max-h-[94vh] w-full max-w-6xl overflow-y-auto rounded-xl bg-white shadow-2xl">
            <!-- Header -->
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
                <!-- Account Information -->
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

                <!-- Business Information -->
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

                <!-- Business Address -->
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
                        <!-- Country -->
                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Country</label>
                            <select v-model="form.country_id" class="h-9 w-full rounded-md border border-gray-300 bg-white px-3 text-xs outline-none transition hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                <option value="">Select country</option>
                                <option v-for="country in props.countries" :key="country.id" :value="country.id">
                                    {{ country.name }} ({{ country.code }})
                                </option>
                            </select>
                            <InputError :message="form.errors.country_id" />
                        </div>

                        <!-- Province -->
                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Province</label>
                            <input v-model="form.province" type="text" placeholder="Province" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.province" />
                        </div>

                        <!-- City -->
                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">City</label>
                            <input v-model="form.city" type="text" placeholder="City" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.city" />
                        </div>

                        <!-- Barangay -->
                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Barangay</label>
                            <input v-model="form.barangay" type="text" placeholder="Barangay" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.barangay" />
                        </div>

                        <!-- Postal Code -->
                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Postal Code</label>
                            <input v-model="form.postal_code" type="text" placeholder="1100" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.postal_code" />
                        </div>
                    </div>
                </section>

                <!-- Business Contact -->
                <section class="mb-2">
                    <div class="mb-3">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-gray-900">Business Contact</h3>
                        <p class="mt-0.5 text-[11px] text-gray-500">Contact information for your business.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- Business Email -->
                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Business Email</label>
                            <input v-model="form.company_email" type="email" placeholder="contact@company.com" class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            <InputError :message="form.errors.company_email" />
                        </div>

                        <!-- Phone -->
                        <div class="flex flex-col">
                            <label class="mb-1.5 text-xs font-medium text-gray-700">Phone Number</label>
                            <div class="flex gap-2">
                                <div class="flex h-9 w-28 items-center rounded-md border border-gray-300 bg-gray-50 px-3 text-xs text-gray-700">
                                    {{ selectedCountry()?.phone_code ?? "Code" }}
                                </div>
                                <input v-model="form.phone" type="text" placeholder="9171234567" class="h-9 flex-1 rounded-md border border-gray-300 px-3 text-xs outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
                            </div>
                            <InputError :message="form.errors.phone" />
                        </div>
                    </div>
                </section>

                <!-- Terms -->
                <div class="mt-5 border-t border-gray-200 pt-4">
                    <div class="flex items-start gap-2">
                        <input v-model="form.terms" type="checkbox" required class="mt-0.5 h-3.5 w-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        <p class="text-xs leading-5 text-gray-600">
                            I agree to the
                            <button type="button" @click="showTermsModal = true" class="font-medium text-blue-600 hover:underline">Terms and Conditions</button>
                            and
                            <button type="button" @click="showPrivacyModal = true" class="font-medium text-blue-600 hover:underline">Privacy Policy</button>.
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

    <TermsModal :show="showTermsModal" @close="showTermsModal = false" />
    <PrivacyModal :show="showPrivacyModal" @close="showPrivacyModal = false" />
</template>
