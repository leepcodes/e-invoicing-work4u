<script setup lang="ts">
import { router } from '@inertiajs/vue3';

defineOptions({
    layout: { breadcrumbs: [{ title: 'Buyer', href: '/buyer' }] },
});

interface Buyer {
    id: number;
    tin: string;
    registered_name: string;
    trade_name: string | null;
    customer_code: string | null;
    address_line_1: string;
    address_line_2: string | null;
    barangay: string;
    city: string;
    province: string;
    postal_code: string | null;
    country_code: string;
    email: string | null;
    phone: string | null;
}

const props = defineProps<{
    buyer: Buyer;
}>();

const goBack = () => router.visit('/buyer');
</script>

<template>
    <div class="p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Buyer Details</h1>
                <p class="mt-1 text-sm text-gray-500">View buyer information.</p>
            </div>

            <button
                type="button"
                @click="goBack"
                class="rounded-lg border border-gray-300 px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-700 hover:bg-gray-50"
            >
                Back
            </button>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="text-xs font-medium text-gray-500">TIN</label>
                    <p class="mt-1 text-sm text-gray-900">{{ buyer.tin || '-' }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500">Registered Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ buyer.registered_name || '-' }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500">Trade Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ buyer.trade_name || '-' }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500">Customer Code</label>
                    <p class="mt-1 text-sm text-gray-900">{{ buyer.customer_code || '-' }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500">Address Line 1</label>
                    <p class="mt-1 text-sm text-gray-900">{{ buyer.address_line_1 || '-' }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500">Address Line 2</label>
                    <p class="mt-1 text-sm text-gray-900">{{ buyer.address_line_2 || '-' }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500">Barangay</label>
                    <p class="mt-1 text-sm text-gray-900">{{ buyer.barangay || '-' }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500">City</label>
                    <p class="mt-1 text-sm text-gray-900">{{ buyer.city || '-' }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500">Province</label>
                    <p class="mt-1 text-sm text-gray-900">{{ buyer.province || '-' }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500">Postal Code</label>
                    <p class="mt-1 text-sm text-gray-900">{{ buyer.postal_code || '-' }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500">Country</label>
                    <p class="mt-1 text-sm text-gray-900">{{ buyer.country_code || '-' }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500">Email</label>
                    <p class="mt-1 text-sm text-gray-900">{{ buyer.email || '-' }}</p>
                </div>

                <div>
                    <label class="text-xs font-medium text-gray-500">Phone</label>
                    <p class="mt-1 text-sm text-gray-900">{{ buyer.phone || '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
