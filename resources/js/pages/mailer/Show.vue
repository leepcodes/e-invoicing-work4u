<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft, Mail } from '@lucide/vue';

interface EmailJobItem {
    id: number;
    invoice_id: number;
    buyer_name: string;
    status: string;
    sent_at: string | null;
}

interface EmailJob {
    id: number;
    email: string;
    subject: string | null;
    status: string;
    sent_items: number;
    created_at: string | null;
    items: EmailJobItem[];
}

defineProps<{ emailJob: EmailJob }>();

const goBack = () => {
    router.visit('/mailer');
};
</script>

<template>
    <Head :title="`Mailer #${emailJob.id}`" />

    <div class="space-y-6 p-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <Mail class="h-5 w-5 text-gray-600" />
                    <h1 class="text-xl font-semibold">Mailer #{{ emailJob.id }}</h1>
                </div>
                <p class="mt-1 text-sm text-gray-500">{{ emailJob.email }}</p>
            </div>

            <button type="button" @click="goBack" class="inline-flex items-center gap-2 rounded-md border px-3 py-2 text-sm hover:bg-gray-50">
                <ArrowLeft class="h-4 w-4" />
                Back
            </button>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <div class="rounded-lg border bg-white p-4">
                <p class="text-xs text-gray-500">Status</p>
                <p class="mt-1 text-sm font-semibold">{{ emailJob.status }}</p>
            </div>

            <div class="rounded-lg border bg-white p-4">
                <p class="text-xs text-gray-500">Recipient</p>
                <p class="mt-1 truncate text-sm font-semibold">{{ emailJob.email }}</p>
            </div>

            <div class="rounded-lg border bg-white p-4">
                <p class="text-xs text-gray-500">Invoices Sent</p>
                <p class="mt-1 text-sm font-semibold">{{ emailJob.sent_items }}</p>
            </div>

            <div class="rounded-lg border bg-white p-4">
                <p class="text-xs text-gray-500">Created</p>
                <p class="mt-1 text-sm font-semibold">{{ emailJob.created_at ?? '-' }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border bg-white">
            <div class="border-b px-4 py-3">
                <h2 class="text-sm font-semibold">Email Job Items</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-3">Invoice ID</th>
                            <th class="px-4 py-3">Buyer Name</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Sent At</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        <tr v-for="item in emailJob.items" :key="item.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ item.invoice_id }}</td>
                            <td class="px-4 py-3">{{ item.buyer_name }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-medium" :class="{
                                    'bg-green-100 text-green-700': item.status === 'SENT',
                                    'bg-yellow-100 text-yellow-700': item.status === 'PROCESSING',
                                    'bg-red-100 text-red-700': item.status === 'FAILED',
                                    'bg-gray-100 text-gray-700': !['SENT', 'PROCESSING', 'FAILED'].includes(item.status),
                                }">
                                    {{ item.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ item.sent_at ?? '-' }}</td>
                        </tr>

                        <tr v-if="!emailJob.items.length">
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No invoices found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
