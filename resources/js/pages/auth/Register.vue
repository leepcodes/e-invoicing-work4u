<script setup lang="ts">

import { ref } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import { Eye, EyeOff } from "@lucide/vue";
import InputError from "@/components/InputError.vue";
import TermsModal from "@/components/landing/TermsModal.vue";
import PrivacyModal from "@/components/landing/PrivacyModal.vue";
import type { TrialRegistrationData } from "@/types/forms/RegistrationData";

defineOptions({
    layout: {
        title: "Create an account",
        description: "Start your 7-day free trial",
    },
});

const showPassword = ref(false);
const showTermsModal = ref(false);
const showPrivacyModal = ref(false);

const form = useForm<TrialRegistrationData>({
    full_name: "",
    company_name: "",
    address: "",
    email: "",
    password: "",
    phone: "",
    terms: false,
});

const submit = () => {
    form.post("/register", {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};

const openTermsModal = () => {
   showTermsModal.value = true;
};
const closeTermsModal = () => {
    showTermsModal.value = false;
};
const openPrivacyModal = () => {
    showPrivacyModal.value = true;
};
const closePrivacyModal = () => {
    showPrivacyModal.value = false;
};
</script>

<template>
    <Head title="Register" />
        <div class="min-h-screen flex items-centerjustify-centerbg-gray-50 px-4 py-10">
            <div
                class="
                    bg-white
                    rounded-2xl
                    shadow-lg
                    p-8
                    w-full
                    max-w-2xl
                "
                >
                <h1 class="text-3xl font-bold text-black">
                    Create Your Free Account
                </h1>

                <p class="mt-2 text-gray-500">Start your 7-day free trial. No credit card required.</p>

                <form @submit.prevent="submit" class="mt-8 space-y-5">

                    <!-- Name + Company -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 font-semibold">
                                Full Name
                            </label>
                            <input
                                v-model="form.full_name"
                                type="text"
                                placeholder="Juan Dela Cruz"
                                class="
                                    w-full
                                    border
                                    rounded-lg
                                    p-3
                                "
                            />
                            <InputError :message="form.errors.full_name"/>
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">
                                Company Name
                            </label>
                            <input
                                v-model="form.company_name"
                                type="text"
                                placeholder="ABC Corporation"
                                class="
                                    w-full
                                    border
                                    rounded-lg
                                    p-3
                                "
                            />
                            <InputError :message="form.errors.company_name"/>
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label class="block mb-2 font-semibold">
                            Address
                        </label>
                        <input
                            v-model="form.address"
                            type="text"
                            placeholder="Company Address"
                            class="
                                w-full
                                border
                                rounded-lg
                                p-3
                            "
                        />
                        <InputError :message="form.errors.address"/>
                    </div>

                    <!-- Email Phone -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 font-semibold">
                                Email Address
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="email@gmail.com"
                                class="
                                    w-full
                                    border
                                    rounded-lg
                                    p-3
                                "
                            />
                            <InputError :message="form.errors.email"/>
                        </div>
                        <div>
                            <label class="block mb-2 font-semibold">
                                Phone Number
                            </label>
                            <input
                                v-model="form.phone"
                                type="text"
                                placeholder="+63 912 345 6789"
                                class="
                                    w-full
                                    border
                                    rounded-lg
                                    p-3
                                "
                            />
                            <InputError :message="form.errors.phone"/>
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block mb-2 font-semibold">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                v-model="form.password"
                                :type="showPassword ? 'text':'password'"
                                placeholder="********"
                                class="
                                    w-full
                                    border
                                    rounded-lg
                                    p-3
                                    pr-12
                                "
                            />
                            <Eye
                                v-if="!showPassword"
                                @click="showPassword=true"
                                class="
                                    absolute
                                    right-3
                                    top-3
                                    cursor-pointer
                                    text-gray-500
                                "
                            />
                            <EyeOff
                                v-else
                                @click="showPassword=false"
                                class="
                                    absolute
                                    right-3
                                    top-3
                                    cursor-pointer
                                    text-gray-500
                                "
                            />
                        </div>
                        <InputError :message="form.errors.password"/>
                    </div>

                    <!-- Terms -->
                    <div class="flex gap-2">
                        <input v-model="form.terms" type="checkbox"/>
                        <p class="text-sm text-gray-600">
                        I agree to the
                        <button
                            type="button"
                            @click="openTermsModal"
                            class="text-blue-600 underline"
                        >
                        Terms and Conditions
                        </button>
                        and
                        <button
                            type="button"
                            @click="openPrivacyModal"
                            class="text-blue-600 underline"
                        >
                        Privacy Policy
                        </button>
                        </p>
                    </div>
                    <InputError :message="form.errors.terms"/>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="
                            w-full
                            bg-blue-600
                            text-white
                            py-3
                            rounded-xl
                            font-semibold
                            disabled:opacity-50
                        "
                    >
                    {{ form.processing
                        ? "Creating account..."
                        : "Create Free Trial"
                    }}
                    </button>
                </form>
        </div>
    </div>

<TermsModal
    :show="showTermsModal"
    @close="closeTermsModal"
/>
<PrivacyModal
    :show="showPrivacyModal"
    @close="closePrivacyModal"
/>
</template>
