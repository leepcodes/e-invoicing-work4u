<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';

interface Buyer {
    id: number;
    tin: string | null;
    registered_name: string;
    trade_name: string | null;
    customer_code: string;
    address_line_1: string;
    address_line_2: string | null;
    barangay: string | null;
    city: string;
    province: string;
    postal_code: string | null;
    country_code: string;
    email: string | null;
    phone: string | null;
}

const props = defineProps<{ buyer: Buyer }>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Buyer', href: '/buyer' },
            { title: 'Edit Buyer', href: '/buyer' },
        ],
    },
});

const form = useForm({
    tin: props.buyer.tin,
    registered_name: props.buyer.registered_name,
    trade_name: props.buyer.trade_name,
    customer_code: props.buyer.customer_code,
    address_line_1: props.buyer.address_line_1,
    address_line_2: props.buyer.address_line_2,
    barangay: props.buyer.barangay,
    city: props.buyer.city,
    province: props.buyer.province,
    postal_code: props.buyer.postal_code,
    country_code: props.buyer.country_code,
    email: props.buyer.email,
    phone: props.buyer.phone,
});

const goBack = () => router.visit('/buyer');
const updateBuyer = () => form.put(`/buyer/${props.buyer.id}`, { preserveScroll: true });
</script>

<template>
    <div class="p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Edit Buyer</h1>
                <p class="mt-1 text-sm text-gray-500">Update buyer information.</p>
            </div>
            <button type="button" @click="goBack" class="rounded-lg border border-gray-300 px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-50">
                Back
            </button>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="tin" class="mb-2 block text-sm font-medium text-gray-700">TIN</label>
                    <input id="tin" v-model="form.tin" type="text" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" :class="{ 'border-red-500': form.errors.tin }" />
                    <p v-if="form.errors.tin" class="mt-1 text-sm text-red-600">{{ form.errors.tin }}</p>
                </div>

                <div>
                    <label for="customer_code" class="mb-2 block text-sm font-medium text-gray-700">Customer Code</label>
                    <input id="customer_code" v-model="form.customer_code" type="text" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" :class="{ 'border-red-500': form.errors.customer_code }" />
                    <p v-if="form.errors.customer_code" class="mt-1 text-sm text-red-600">{{ form.errors.customer_code }}</p>
                </div>

                <div class="lg:col-span-2">
                    <label for="registered_name" class="mb-2 block text-sm font-medium text-gray-700">Registered Name</label>
                    <input id="registered_name" v-model="form.registered_name" type="text" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" :class="{ 'border-red-500': form.errors.registered_name }" />
                    <p v-if="form.errors.registered_name" class="mt-1 text-sm text-red-600">{{ form.errors.registered_name }}</p>
                </div>

                <div class="lg:col-span-2">
                    <label for="trade_name" class="mb-2 block text-sm font-medium text-gray-700">Trade Name</label>
                    <input id="trade_name" v-model="form.trade_name" type="text" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" :class="{ 'border-red-500': form.errors.trade_name }" />
                    <p v-if="form.errors.trade_name" class="mt-1 text-sm text-red-600">{{ form.errors.trade_name }}</p>
                </div>

                <div class="lg:col-span-2">
                    <label for="address_line_1" class="mb-2 block text-sm font-medium text-gray-700">Address Line 1</label>
                    <input id="address_line_1" v-model="form.address_line_1" type="text" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" :class="{ 'border-red-500': form.errors.address_line_1 }" />
                    <p v-if="form.errors.address_line_1" class="mt-1 text-sm text-red-600">{{ form.errors.address_line_1 }}</p>
                </div>

                <div class="lg:col-span-2">
                    <label for="address_line_2" class="mb-2 block text-sm font-medium text-gray-700">Address Line 2</label>
                    <input id="address_line_2" v-model="form.address_line_2" type="text" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" :class="{ 'border-red-500': form.errors.address_line_2 }" />
                    <p v-if="form.errors.address_line_2" class="mt-1 text-sm text-red-600">{{ form.errors.address_line_2 }}</p>
                </div>

                <div>
                    <label for="barangay" class="mb-2 block text-sm font-medium text-gray-700">Barangay</label>
                    <input id="barangay" v-model="form.barangay" type="text" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" :class="{ 'border-red-500': form.errors.barangay }" />
                    <p v-if="form.errors.barangay" class="mt-1 text-sm text-red-600">{{ form.errors.barangay }}</p>
                </div>

                <div>
                    <label for="city" class="mb-2 block text-sm font-medium text-gray-700">City</label>
                    <input id="city" v-model="form.city" type="text" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" :class="{ 'border-red-500': form.errors.city }" />
                    <p v-if="form.errors.city" class="mt-1 text-sm text-red-600">{{ form.errors.city }}</p>
                </div>

                <div>
                    <label for="province" class="mb-2 block text-sm font-medium text-gray-700">Province</label>
                    <input id="province" v-model="form.province" type="text" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" :class="{ 'border-red-500': form.errors.province }" />
                    <p v-if="form.errors.province" class="mt-1 text-sm text-red-600">{{ form.errors.province }}</p>
                </div>

                <div>
                    <label for="postal_code" class="mb-2 block text-sm font-medium text-gray-700">Postal Code</label>
                    <input id="postal_code" v-model="form.postal_code" type="text" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" :class="{ 'border-red-500': form.errors.postal_code }" />
                    <p v-if="form.errors.postal_code" class="mt-1 text-sm text-red-600">{{ form.errors.postal_code }}</p>
                </div>

                <div>
                    <label for="country_code" class="mb-2 block text-sm font-medium text-gray-700">Country Code</label>
                    <select id="country_code" v-model="form.country_code" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" :class="{ 'border-red-500': form.errors.country_code }">
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
                    <p v-if="form.errors.country_code" class="mt-1 text-sm text-red-600">{{ form.errors.country_code }}</p>
                </div>

                <div class="lg:col-span-2">
                    <label for="email" class="mb-2 block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" v-model="form.email" type="email" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" :class="{ 'border-red-500': form.errors.email }" />
                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                </div>

                <div class="lg:col-span-2">
                    <label for="phone" class="mb-2 block text-sm font-medium text-gray-700">Phone</label>
                    <input id="phone" v-model="form.phone" type="text" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500" :class="{ 'border-red-500': form.errors.phone }" />
                    <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t border-gray-200 pt-5">
                <button type="button" @click="goBack" :disabled="form.processing" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50">
                    Cancel
                </button>
                <button type="button" @click="updateBuyer" :disabled="form.processing" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">
                    {{ form.processing ? 'Updating...' : 'Update Buyer' }}
                </button>
            </div>
        </div>
    </div>
</template>
