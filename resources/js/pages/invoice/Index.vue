<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { CheckCircle2 } from '@lucide/vue';
import { router } from '@inertiajs/vue3';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Invoice', href: '/invoice' }],
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
    buyer_id: number | null;
    buyer: Buyer | null;
    buyer_name: string;
    document_type: string;
    invoice_number: string;
    reference_number: string | null;
    invoice_date: string;
    due_date: string;
    currency_code: string;
    total_amount: number | string;
    amount_paid: number | string;
    amount_due: number | string;
    payment_status: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedInvoices {
    data: Invoice[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
}

interface Filters {
    search: string;
    status: string;
    invoice_date_from: string;
    invoice_date_to: string;
    due_date_from: string;
    due_date_to: string;
}

interface Props {
    invoices: PaginatedInvoices;
    filters: Filters;
    flash?: {
        success?: string;
        error?: string;
    };
}

const getBuyerName = (invoice: Invoice) =>
    invoice.buyer?.registered_name || invoice.buyer_name || '—';

const props = defineProps<Props>();

const search = ref(props.filters.search ?? '');
const statusFilter = ref(props.filters.status ?? '');
const invoiceDateFrom = ref(props.filters.invoice_date_from ?? '');
const invoiceDateTo = ref(props.filters.invoice_date_to ?? '');
const dueDateFrom = ref(props.filters.due_date_from ?? '');
const dueDateTo = ref(props.filters.due_date_to ?? '');

const goCreate = () => router.visit('/invoice/create');
const goImport = () => router.visit('/invoice/import');

const applyFilters = () => {
    router.get('/invoice', {
        search: search.value,
        status: statusFilter.value,
        invoice_date_from: invoiceDateFrom.value,
        invoice_date_to: invoiceDateTo.value,
        due_date_from: dueDateFrom.value,
        due_date_to: dueDateTo.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    search.value = '';
    statusFilter.value = '';
    invoiceDateFrom.value = '';
    invoiceDateTo.value = '';
    dueDateFrom.value = '';
    dueDateTo.value = '';

    router.get('/invoice', {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const goToPage = (url: string | null) => {
    if (!url) return;
    router.visit(url, { preserveState: true, preserveScroll: true });
};

const viewInvoice = (invoice: Invoice) => router.visit(`/invoice/${invoice.id}`);
const editInvoice = (invoice: Invoice) => router.visit(`/invoice/${invoice.id}/edit`);

const deleteInvoice = (invoice: Invoice) => {
    if (!confirm(`Delete invoice ${invoice.invoice_number}?`)) return;
    router.delete(`/invoice/${invoice.id}`, { preserveScroll: true });
};

const formatAmount = (value: number | string) => Number(value || 0).toFixed(2);

const statusClass = (status: string) => ({
    'bg-yellow-100 text-yellow-700': status === 'UNPAID',
    'bg-green-100 text-green-700': status === 'PAID',
    'bg-blue-100 text-blue-700': status === 'PARTIALLY PAID',
});

const formatDate = (date: string) => {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const successMessage = ref('');

onMounted(() => {
    if (!props.flash?.success) return;

    successMessage.value = props.flash.success;

    setTimeout(() => {
        successMessage.value = '';
    }, 5000);
});

</script>

<template>
    <div class="p-6 print:hidden">
        <div
            v-if="successMessage"
            class="mb-4 flex items-center gap-3 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
        >
            <CheckCircle2 class="h-5 w-5 shrink-0 text-green-600" />
            <span>{{ successMessage }}</span>
        </div>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Invoice</h1>
                <p class="mt-1 text-sm text-gray-500">Manage and view your invoices.</p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" class="rounded-lg border border-gray-300 bg-green-600 px-5 py-3 text-xs font-semibold uppercase tracking-wider text-white hover:bg-green-700" @click="goImport">
                    Import CSV
                </button>
                <button type="button" class="rounded-lg bg-blue-600 px-5 py-3 text-xs font-semibold uppercase tracking-wider text-white hover:bg-blue-700" @click="goCreate">
                    Create Invoice
                </button>
            </div>
        </div>

        <div class="mb-4 rounded-lg border border-gray-200 bg-white p-3 print:hidden">
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-medium text-gray-600">Search</label>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Invoice, buyer, or reference..."
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        @keyup.enter="applyFilters"
                    />
                </div>

                <div class="w-36">
                    <label class="mb-1 block text-xs font-medium text-gray-600">Invoice From</label>
                    <input v-model="invoiceDateFrom" type="date" class="w-full rounded-md border border-gray-300 px-2 py-2 text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                </div>

                <div class="w-36">
                    <label class="mb-1 block text-xs font-medium text-gray-600">Invoice To</label>
                    <input v-model="invoiceDateTo" type="date" class="w-full rounded-md border border-gray-300 px-2 py-2 text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                </div>

                <div class="w-36">
                    <label class="mb-1 block text-xs font-medium text-gray-600">Due From</label>
                    <input v-model="dueDateFrom" type="date" class="w-full rounded-md border border-gray-300 px-2 py-2 text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                </div>

                <div class="w-36">
                    <label class="mb-1 block text-xs font-medium text-gray-600">Due To</label>
                    <input v-model="dueDateTo" type="date" class="w-full rounded-md border border-gray-300 px-2 py-2 text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                </div>

                <div class="w-32">
                    <label class="mb-1 block text-xs font-medium text-gray-600">Status</label>
                    <select v-model="statusFilter" class="w-full rounded-md border border-gray-300 bg-white px-2 py-2 text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="UNPAID">Unpaid</option>
                        <option value="PARTIALLY PAID">Partially Paid</option>
                        <option value="PAID">Paid</option>
                    </select>
                </div>

                <button type="button" class="rounded-md bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700" @click="applyFilters">Filter</button>
                <button type="button" class="rounded-md border border-gray-300 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50" @click="clearFilters">Clear</button>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
            <table class="w-full table-fixed divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-[10%] px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Invoice Number</th>
                        <th class="w-[13%] px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Buyer</th>
                        <th class="w-[10%] px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Reference</th>
                        <th class="w-[9%] px-3 py-2.5 text-right text-[10px] font-semibold uppercase tracking-wider text-gray-500">Total</th>
                        <th class="w-[9%] px-3 py-2.5 text-right text-[10px] font-semibold uppercase tracking-wider text-gray-500">Paid</th>
                        <th class="w-[9%] px-3 py-2.5 text-right text-[10px] font-semibold uppercase tracking-wider text-gray-500">Due</th>
                        <th class="w-[10%] px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Invoice Date</th>
                        <th class="w-[10%] px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Due Date</th>
                        <th class="w-[9%] px-3 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="w-[11%] px-3 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    <tr v-if="invoices.data.length === 0">
                        <td colspan="10" class="px-4 py-10 text-center">
                            <p class="text-sm font-medium text-gray-700">No invoices found</p>
                            <p class="mt-1 text-sm text-gray-500">Try changing your search or filters.</p>
                        </td>
                    </tr>

                    <tr v-for="invoice in invoices.data" :key="invoice.id" class="transition hover:bg-gray-50">
                        <td class="px-3 py-3">
                            <span class="block truncate text-xs font-medium text-gray-900" :title="invoice.invoice_number">{{ invoice.invoice_number }}</span>
                        </td>

                        <td class="px-3 py-3">
                            <span class="block truncate text-xs font-medium text-gray-900" :title="getBuyerName(invoice)">{{ getBuyerName(invoice) }}</span>
                        </td>

                        <td class="px-3 py-3">
                            <span class="block truncate text-xs text-gray-600" :title="invoice.reference_number ?? ''">{{ invoice.reference_number ?? '—' }}</span>
                        </td>

                        <td class="px-3 py-3 text-right text-xs text-gray-600">
                            <span class="whitespace-nowrap">{{ invoice.currency_code }} {{ formatAmount(invoice.total_amount) }}</span>
                        </td>

                        <td class="px-3 py-3 text-right text-xs text-gray-600">
                            <span class="whitespace-nowrap">{{ invoice.currency_code }} {{ formatAmount(invoice.amount_paid) }}</span>
                        </td>

                        <td class="px-3 py-3 text-right text-xs text-gray-600">
                            <span class="whitespace-nowrap">{{ invoice.currency_code }} {{ formatAmount(invoice.amount_due) }}</span>
                        </td>

                        <td class="px-3 py-3 text-xs text-gray-600">
                            <span class="whitespace-nowrap">{{ formatDate(invoice.invoice_date) }}</span>
                        </td>

                        <td class="px-3 py-3 text-xs text-gray-600">
                            <span class="whitespace-nowrap">{{ formatDate(invoice.due_date) }}</span>
                        </td>

                        <td class="px-3 py-3 text-center">
                            <span class="inline-flex rounded-full px-2 py-1 text-[9px] font-medium" :class="statusClass(invoice.payment_status)">
                                {{ invoice.payment_status }}
                            </span>
                        </td>

                        <td class="px-2 py-3">
                            <div class="flex justify-center gap-1">
                                <button type="button" class="rounded border border-gray-300 px-2 py-1 text-[10px] font-medium text-gray-700 hover:bg-gray-100" @click="viewInvoice(invoice)">View</button>
                                <button type="button" class="rounded border border-gray-300 px-2 py-1 text-[10px] font-medium text-gray-700 hover:bg-gray-100" @click="editInvoice(invoice)">Edit</button>
                                <button type="button" class="rounded border border-gray-300 px-2 py-1 text-[10px] font-medium text-red-600 hover:bg-red-50" @click="deleteInvoice(invoice)">Delete</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="invoices.last_page > 1" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3">
                <div class="text-xs text-gray-500">
                    Showing
                    <span class="font-medium text-gray-700">{{ invoices.from }}</span> -
                    <span class="font-medium text-gray-700">{{ invoices.to }}</span>
                    of
                    <span class="font-medium text-gray-700">{{ invoices.total }}</span>
                </div>

                <div class="flex items-center gap-1">
                    <button
                        v-for="link in invoices.links"
                        :key="link.label"
                        type="button"
                        :disabled="!link.url"
                        class="min-w-[30px] rounded-md border px-2 py-1.5 text-[11px] font-medium"
                        :class="link.active ? 'border-blue-600 bg-blue-600 text-white' : link.url ? 'border-gray-300 text-gray-600 hover:bg-gray-50' : 'cursor-not-allowed border-gray-200 text-gray-300'"
                        v-html="link.label"
                        @click="link.url && goToPage(link.url)"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

