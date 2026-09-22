<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Buyer', href: '/buyer' },
            { title: 'Create Buyer', href: '/buyer/create' },
        ],
    },
});

const form = useForm({
    tin: '',
    registered_name: '',
    trade_name: '',
    customer_code: '',
    address_line_1: '',
    address_line_2: '',
    barangay: '',
    city: '',
    province: '',
    postal_code: '',
    country_code: 'PH',
    email: '',
    phone: '',
});

const goBack = () => router.visit('/buyer');
const submit = () => form.post('/buyer', { onSuccess: () => form.reset() });
</script>

<template>
    <div class="p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Create Buyer</h1>
                <p class="mt-1 text-sm text-gray-500">Add a new buyer.</p>
            </div>
            <button @click="goBack" class="rounded-lg border border-gray-300 px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-50">
                Back
            </button>
        </div>

        <form @submit.prevent="submit" class="rounded-lg border border-gray-200 bg-white p-6">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">TIN</label>
                    <input v-model="form.tin" type="text" placeholder="987-654-321-000" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    <p v-if="form.errors.tin" class="mt-1 text-xs text-red-500">{{ form.errors.tin }}</p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Customer Code</label>
                    <input v-model="form.customer_code" type="text" placeholder="CUST-00001" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    <p v-if="form.errors.customer_code" class="mt-1 text-xs text-red-500">{{ form.errors.customer_code }}</p>
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Registered Name</label>
                    <input v-model="form.registered_name" type="text" placeholder="ABC COMPANY INC." class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    <p v-if="form.errors.registered_name" class="mt-1 text-xs text-red-500">{{ form.errors.registered_name }}</p>
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Trade Name</label>
                    <input v-model="form.trade_name" type="text" placeholder="ABC Company" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    <p v-if="form.errors.trade_name" class="mt-1 text-xs text-red-500">{{ form.errors.trade_name }}</p>
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Address Line 1</label>
                    <input v-model="form.address_line_1" type="text" placeholder="123 Example Street" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    <p v-if="form.errors.address_line_1" class="mt-1 text-xs text-red-500">{{ form.errors.address_line_1 }}</p>
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Address Line 2</label>
                    <input v-model="form.address_line_2" type="text" placeholder="Building / Unit / Floor" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Barangay</label>
                    <input v-model="form.barangay" type="text" placeholder="Barangay 1" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">City</label>
                    <input v-model="form.city" type="text" placeholder="Makati City" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    <p v-if="form.errors.city" class="mt-1 text-xs text-red-500">{{ form.errors.city }}</p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Province</label>
                    <input v-model="form.province" type="text" placeholder="Metro Manila" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    <p v-if="form.errors.province" class="mt-1 text-xs text-red-500">{{ form.errors.province }}</p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Postal Code</label>
                    <input v-model="form.postal_code" type="text" placeholder="1200" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Country</label>
                    <select v-model="form.country_code" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="PH">Philippines (PH)</option>
                        <option value="US">United States (US)</option>
                        <option value="SG">Singapore (SG)</option>
                        <option value="MY">Malaysia (MY)</option>
                        <option value="TH">Thailand (TH)</option>
                        <option value="ID">Indonesia (ID)</option>
                        <option value="JP">Japan (JP)</option>
                        <option value="KR">South Korea (KR)</option>
                        <option value="CN">China (CN)</option>
                        <option value="AU">Australia (AU)</option>
                    </select>
                    <p v-if="form.errors.country_code" class="mt-1 text-xs text-red-500">{{ form.errors.country_code }}</p>
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Email</label>
                    <input v-model="form.email" type="email" placeholder="billing@example.com" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Phone</label>
                    <input v-model="form.phone" type="text" placeholder="+63281234567" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" />
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t border-gray-200 pt-5">
                <button type="button" @click="goBack" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" :disabled="form.processing" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">
                    {{ form.processing ? 'Saving...' : 'Save Buyer' }}
                </button>
            </div>
        </form>
    </div>
</template>
