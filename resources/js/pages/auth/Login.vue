<script setup lang="ts">
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Eye, EyeOff } from "@lucide/vue";
import InputError from "@/components/InputError.vue";

defineOptions({ layout: [] });

defineProps<{ errors: Record<string, string> }>();

const showPassword = ref(false);

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post("/login", {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="flex min-h-screen w-full items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-sm rounded-xl border border-gray-200 bg-white p-6 shadow-lg">

            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900">
                    Welcome Back
                </h2>

                <p class="mt-0.5 text-xs text-gray-500">
                    Login to access your account.
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-4">

                <!-- Email -->
                <div class="flex flex-col">
                    <label class="mb-1.5 text-xs font-medium text-gray-700">
                        Email Address <span class="text-red-500">*</span>
                    </label>

                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="email@company.com"
                        class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs text-gray-900 outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                    />

                    <InputError :message="form.errors.email" />
                </div>

                <!-- Password -->
                <div class="flex flex-col">
                    <label class="mb-1.5 text-xs font-medium text-gray-700">
                        Password <span class="text-red-500">*</span>
                    </label>

                    <div class="relative">
                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Enter password"
                            class="h-9 w-full rounded-md border border-gray-300 px-3 pr-10 text-xs text-gray-900 outline-none transition placeholder:text-gray-400 hover:border-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        />

                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2"
                        >
                            <Eye
                                v-if="!showPassword"
                                class="h-4 w-4 text-gray-400"
                            />

                            <EyeOff
                                v-else
                                class="h-4 w-4 text-gray-400"
                            />
                        </button>
                    </div>

                    <InputError :message="form.errors.password" />
                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-xs text-gray-600">
                        <input
                            v-model="form.remember"
                            type="checkbox"
                            class="h-3.5 w-3.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />

                        Remember me
                    </label>
                </div>

                <!-- Login Button -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-md bg-blue-600 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ form.processing ? "Logging in..." : "Log in" }}
                </button>
            </form>

            <!-- Register -->
            <div class="mt-5 text-center text-xs text-gray-600">
                Don't have an account?

                <a
                    href="/"
                    class="font-semibold text-blue-600 hover:underline"
                >
                    Sign up
                </a>
            </div>

        </div>
    </div>
</template>
