<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Invoice', href: '/invoice' },
            { title: 'Edit Invoice', href: '#' },
        ],
    },
});

interface Buyer {
    id: number;
    registered_name: string;
    trade_name: string | null;
    customer_code: string | null;
}

interface Invoice {
    id: number;
    buyer_id: number;
    buyer: Buyer | null;
    buyer_name: string | null;
    document_type: string;
    invoice_number: string;
    due_date: string;
    reference_number: string | null;
    invoice_date: string;
    currency_code: string;
    total_amount: number | string;
    amount_paid: number | string;
    amount_due: number | string;
    payment_status: string;
}

interface Props {
    invoice: Invoice;
}

const props = defineProps<Props>();

const form = useForm({
    amount_paid: Number(props.invoice.amount_paid || 0),
    payment_status: props.invoice.payment_status,
});

const formatAmount = (value: number | string) => Number(value || 0).toFixed(2);

const formatDate = (date?: string | null) => {
    if (!date) return '—';

    const [year, month, day] = date.slice(0, 10).split('-');

    return new Date(Number(year), Number(month) - 1, Number(day)).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const amountDue = () => Math.max(Number(props.invoice.total_amount) - Number(form.amount_paid || 0), 0);

const submit = () => form.put(`/invoice/${props.invoice.id}`, { preserveScroll: true });
const cancel = () => router.visit('/invoice');

const getBuyerName = (invoice: Invoice) => invoice.buyer?.registered_name || invoice.buyer_name || '—';
</script>

<template>
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Edit Invoice</h1>
            <p class="mt-1 text-sm text-gray-500">Update the payment information for this invoice.</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-base font-semibold text-gray-900">Invoice Information</h2>
                <p class="mt-1 text-sm text-gray-500">Invoice details are read-only.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Invoice Number</label>
                    <input type="text" :value="invoice.invoice_number" disabled class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-sm text-gray-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Document Type</label>
                    <input type="text" :value="invoice.document_type" disabled class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-sm text-gray-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Buyer</label>
                    <input type="text" :value="getBuyerName(invoice)" disabled class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-sm text-gray-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Reference Number</label>
                    <input type="text" :value="invoice.reference_number ?? '—'" disabled class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-sm text-gray-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Invoice Date</label>
                    <input type="text" :value="formatDate(invoice.invoice_date)" disabled class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-sm text-gray-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Due Date</label>
                    <input type="text" :value="formatDate(invoice.due_date)" disabled class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-sm text-gray-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                    <input type="text" :value="`${invoice.currency_code} ${formatAmount(invoice.total_amount)}`" disabled class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-sm text-gray-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Amount Paid</label>
                    <div class="mt-1 flex">
                        <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-100 px-3 text-sm text-gray-500">
                            {{ invoice.currency_code }}
                        </span>
                        <input v-model="form.amount_paid" type="number" min="0" :max="Number(invoice.total_amount)" step="0.01" class="block w-full rounded-r-md border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <p v-if="form.errors.amount_paid" class="mt-1 text-sm text-red-600">{{ form.errors.amount_paid }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Payment Status</label>
                    <select v-model="form.payment_status" class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="UNPAID">Unpaid</option>
                        <option value="PARTIALLY PAID">Partially Paid</option>
                        <option value="PAID">Paid</option>
                    </select>
                    <p v-if="form.errors.payment_status" class="mt-1 text-sm text-red-600">{{ form.errors.payment_status }}</p>
                </div>
            </div>
        </div>

        <div class="mt-6 rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-base font-semibold text-gray-900">Payment Summary</h2>
            </div>

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-3">
                <div>
                    <p class="text-sm text-gray-500">Total Amount</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ invoice.currency_code }} {{ formatAmount(invoice.total_amount) }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Amount Paid</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ invoice.currency_code }} {{ formatAmount(form.amount_paid) }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Amount Due</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ invoice.currency_code }} {{ formatAmount(amountDue()) }}</p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" class="rounded-md border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50" @click="cancel">
                Cancel
            </button>
            <button type="button" :disabled="form.processing" class="rounded-md bg-blue-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50" @click="submit">
                {{ form.processing ? 'Saving...' : 'Save Changes' }}
            </button>
        </div>
    </div>
</template>
