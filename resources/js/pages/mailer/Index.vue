<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Search, Mail, FileText, CheckCircle2, Eye } from '@lucide/vue';
import { computed, ref } from 'vue';

interface Buyer {
    id: number;
    tin: string;
    registered_name:string;
    email: string | null;
}
interface Invoice
{   id: number;
    invoice_number: string;
    reference_number: string | null;
    invoice_date: string;
    due_date: string | null;
    total_amount: number;
    payment_status: string;
}

const props = defineProps<{ buyers: Buyer[]; invoices: Invoice[]; selectedBuyerId: number | null; }>();
const buyerSearch = ref('');
const invoiceSearch = ref('');
const statusFilter = ref('');
const selectedInvoices = ref<number[]>([]);
const sending = ref(false);

const filteredBuyers = computed(() => props.buyers.filter(buyer => `${buyer.registered_name} ${buyer.tin}`.toLowerCase().includes(buyerSearch.value.toLowerCase())));
const filteredInvoices = computed(() => props.invoices.filter(invoice => {
    const search = invoiceSearch.value.toLowerCase();
    return (invoice.invoice_number.toLowerCase().includes(search) || (invoice.reference_number ?? '').toLowerCase().includes(search)) && (!statusFilter.value || invoice.payment_status === statusFilter.value);
}));
const selectedBuyer = computed(() => props.buyers.find(buyer => buyer.id === props.selectedBuyerId));
const allSelected = computed(() => filteredInvoices.value.length > 0 && filteredInvoices.value.every(invoice => selectedInvoices.value.includes(invoice.id)));

const selectBuyer = (buyerId: number) => {
    selectedInvoices.value = [];
    invoiceSearch.value = '';
    statusFilter.value = '';
    router.get('/mailer', { buyer_id: buyerId }, { preserveState: true, preserveScroll: true });
};

const viewMailer = () => {
    router.visit('/mailer/jobs');
};

const toggleInvoice = (id: number) => selectedInvoices.value.includes(id) ? selectedInvoices.value = selectedInvoices.value.filter(invoiceId => invoiceId !== id) : selectedInvoices.value.push(id);

const toggleAll = () => allSelected.value
    ? selectedInvoices.value = selectedInvoices.value.filter(id => !filteredInvoices.value.some(invoice => invoice.id === id))
    : selectedInvoices.value = [...new Set([...selectedInvoices.value, ...filteredInvoices.value.map(invoice => invoice.id)])];


const successMessage = ref('');

const sendInvoices = () => {
    if (!props.selectedBuyerId || !selectedInvoices.value.length) return;

    sending.value = true;
    successMessage.value = '';

    router.post('/mailer/send', {
        buyer_id: props.selectedBuyerId,
        invoice_ids: selectedInvoices.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            selectedInvoices.value = [];
            successMessage.value = 'Invoice email sent successfully.';

            setTimeout(() => {
                successMessage.value = '';
            }, 5000);
        },
        onFinish: () => sending.value = false,
    });
};
</script>

<template>
    <Head title="Mailer" />

    <div class="space-y-6 p-6">

        <div
        v-if="successMessage"
        class="flex items-center gap-3 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
        >
            <CheckCircle2 class="h-5 w-5 shrink-0 text-green-600" />
            <span>{{ successMessage }}</span>
        </div>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Mailer</h1>
                <p class="text-sm text-gray-500">Select a buyer and send their invoices by email.</p>
            </div>
            <button
                type="button"
                @click="viewMailer"
                class="flex items-center gap-2 rounded-md border px-3 py-2 text-sm font-medium hover:bg-gray-50"
            >
                <Eye class="h-4 w-4" />
                Email Jobs
            </button>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-lg border bg-white">
                <div class="border-b p-4">
                    <h2 class="font-semibold">Buyers</h2>
                    <div class="relative mt-3">
                        <Search class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
                        <input v-model="buyerSearch" type="text" placeholder="Search buyer..." class="w-full rounded-md border py-2 pl-9 pr-3 text-sm" />
                    </div>
                </div>

                <div class="max-h-[600px] overflow-y-auto">
                    <button v-for="buyer in filteredBuyers" :key="buyer.id" type="button" @click="selectBuyer(buyer.id)" class="w-full border-b p-4 text-left hover:bg-gray-50" :class="selectedBuyerId === buyer.id ? 'bg-gray-50' : ''">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium">{{ buyer.registered_name }}</p>
                                <p class="mt-1 text-xs text-gray-500">TIN: {{ buyer.tin }}</p>
                                <p v-if="buyer.email" class="mt-1 text-xs text-gray-500">{{ buyer.email }}</p>
                            </div>
                            <CheckCircle2 v-if="selectedBuyerId === buyer.id" class="h-5 w-5 text-green-600" />
                        </div>
                    </button>
                </div>
            </div>

            <div class="rounded-lg border bg-white lg:col-span-2">
                <div class="border-b p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="font-semibold">{{ selectedBuyer?.registered_name ?? 'Invoices' }}</h2>
                            <p class="text-xs text-gray-500">{{ selectedBuyer?.email ?? 'Select a buyer first' }}</p>
                        </div>

                        <button v-if="selectedInvoices.length" type="button" @click="sendInvoices" :disabled="sending" class="flex items-center gap-2 rounded-md bg-black px-4 py-2 text-sm font-medium text-white disabled:opacity-50">
                            <Mail class="h-4 w-4" />
                            {{ sending ? 'Sending...' : `Send (${selectedInvoices.length})` }}
                        </button>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="relative">
                            <Search class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
                            <input v-model="invoiceSearch" type="text" placeholder="Search invoice..." class="w-full rounded-md border py-2 pl-9 pr-3 text-sm" :disabled="!selectedBuyerId" />
                        </div>

                        <select v-model="statusFilter" class="rounded-md border px-3 py-2 text-sm" :disabled="!selectedBuyerId">
                            <option value="">All statuses</option>
                            <option value="PAID">Paid</option>
                            <option value="UNPAID">Unpaid</option>
                            <option value="PARTIALLY_PAID">Partially Paid</option>
                        </select>
                    </div>
                </div>

                <div v-if="selectedBuyerId">
                    <div v-if="filteredInvoices.length">
                        <div class="flex items-center gap-3 border-b bg-gray-50 px-4 py-3 text-sm">
                            <input type="checkbox" :checked="allSelected" @change="toggleAll" />
                            <span class="font-medium">Select all</span>
                            <span class="text-xs text-gray-500">{{ filteredInvoices.length }} invoices</span>
                        </div>

                        <div v-for="invoice in filteredInvoices" :key="invoice.id" class="flex items-center gap-4 border-b px-4 py-4 hover:bg-gray-50">
                            <input type="checkbox" :checked="selectedInvoices.includes(invoice.id)" @change="toggleInvoice(invoice.id)" />
                            <FileText class="h-5 w-5 shrink-0 text-gray-400" />

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium">{{ invoice.invoice_number }}</p>
                                <p class="text-xs text-gray-500">Ref: {{ invoice.reference_number ?? '—' }}</p>
                            </div>

                            <div class="text-right">
                                <p class="text-sm font-medium">₱{{ Number(invoice.total_amount).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}</p>
                                <p class="text-xs text-gray-500">{{ invoice.invoice_date }}</p>
                            </div>

                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs">{{ invoice.payment_status }}</span>
                        </div>
                    </div>

                    <div v-else class="p-10 text-center text-sm text-gray-500">No invoices found.</div>
                </div>

                <div v-else class="p-10 text-center text-sm text-gray-500">Select a buyer to view their invoices.</div>
            </div>
        </div>
    </div>
</template>
