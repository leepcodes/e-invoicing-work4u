<script setup lang="ts">
import seller from '@/routes/seller';
import { computed, onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { CheckCircle2 } from '@lucide/vue';

interface InvoiceItem {
    id: number;
    line_number: number | null;
    item_code: string | null;
    description: string;
    quantity: number | string;
    unit_code: string | null;
    unit_price: number | string;
    gross_amount: number | string;
    discount_amount: number | string;
    net_amount: number | string;
    tax_type: string | null;
    tax_category: string | null;
    tax_rate: number | string | null;
    taxable_amount: number | string;
    tax_amount: number | string;
    line_total: number | string;
}

interface Seller {
    id: number;
    user_id: number;
    tin: string | null;
    registered_name: string;
    trade_name: string | null;
    branch_code: string | null;
    address_line_1: string | null;
    address_line_2: string | null;
    barangay: string | null;
    city: string | null;
    province: string | null;
    postal_code: string | null;
    country_code: string | null;
    company_email: string | null;
    phone: string | null;
    logo: string | null;
}

interface Buyer {
    id: number;
    seller_id: number;
    tin: string | null;
    registered_name: string;
    trade_name: string | null;
    customer_code: string;
    address_line_1: string | null;
    address_line_2: string | null;
    barangay: string | null;
    city: string;
    province: string | null;
    postal_code: string | null;
    country_code: string | null;
    email: string | null;
    phone: string | null;
}

interface Invoice {
    id: number;
    seller: Seller;
    buyer: Buyer;

    buyer_name: string;
    buyer_tin: string | null;
    buyer_trade_name: string | null;
    buyer_address_line_1: string | null;
    buyer_address_line_2: string | null;
    buyer_barangay: string | null;
    buyer_city: string;
    buyer_province: string | null;
    buyer_postal_code: string | null;
    buyer_country_code: string | null;
    buyer_email: string | null;
    buyer_phone: string | null;

    document_type: string | null;
    invoice_number: string;
    invoice_date: string;
    invoice_time: string | null;
    due_date: string | null;
    reference_number: string | null
    purchase_order_number: string | null;
    currency_code: string;
    accounting_currency_code: string | null;
    exchange_rate: number | string | null;
    exchange_rate_date: string | null;
    exchange_rate_source: string | null;
    gross_amount: number | string;
    discount_amount: number | string;
    taxable_amount: number | string;
    vat_amount: number | string;
    withholding_tax_amount: number | string;
    other_tax_amount: number | string;
    net_amount: number | string;
    total_amount: number | string;
    amount_paid: number | string;
    amount_due: number | string;
    accounting_gross_amount: number | string | null;
    accounting_taxable_amount: number | string | null;
    accounting_vat_amount: number | string | null;
    accounting_total_amount: number | string | null;
    accounting_amount_due: number | string | null;
    payment_terms: string | null;
    payment_method: string | null;
    payment_status: string | null;
    fiscal_year: string | null;
    remarks: string | null;
    items: InvoiceItem[];
}

const getBuyerName = (invoice: Invoice) => invoice.buyer?.registered_name || invoice.buyer_name || '—';
const getBuyerTin = (invoice: Invoice) => invoice.buyer?.tin || invoice.buyer_tin || '—';
const getBuyerTradeName = (invoice: Invoice) => invoice.buyer?.trade_name || invoice.buyer_trade_name || '—';
const getBuyerAddress1 = (invoice: Invoice) => invoice.buyer?.address_line_1 || invoice.buyer_address_line_1 || '—';
const getBuyerAddress2 = (invoice: Invoice) => invoice.buyer?.address_line_2 || invoice.buyer_address_line_2 || '';
const getBuyerBarangay = (invoice: Invoice) => invoice.buyer?.barangay || invoice.buyer_barangay || '';
const getBuyerCity = (invoice: Invoice) => invoice.buyer?.city || invoice.buyer_city || '';
const getBuyerProvince = (invoice: Invoice) => invoice.buyer?.province || invoice.buyer_province || '';
const getBuyerPostalCode = (invoice: Invoice) => invoice.buyer?.postal_code || invoice.buyer_postal_code || '';
const getBuyerCountry = (invoice: Invoice) => invoice.buyer?.country_code || invoice.buyer_country_code || 'PH';
const getBuyerCustomerCode = (invoice: Invoice) => invoice.buyer?.customer_code || '—';
const getBuyerEmail = (invoice: Invoice) => invoice.buyer?.email || invoice.buyer_email || '';
const getBuyerPhone = (invoice: Invoice) => invoice.buyer?.phone || invoice.buyer_phone || '';

const props = defineProps<{ invoice: Invoice }>();

const toNumber = (value: number | string | null | undefined): number => {
    if (value === null || value === undefined) return 0;
    return typeof value === 'number' ? value : parseFloat(value);
};

const formatMoney = (value: number | string | null | undefined) => {
    return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(toNumber(value));
};

const formatDate = (value: string | null | undefined) => {
    if (!value) return '-';
    return new Date(value).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: '2-digit' });
};

const hasVat = computed(() => toNumber(props.invoice.vat_amount) > 0);
const hasDiscount = computed(() => toNumber(props.invoice.discount_amount) > 0);
const hasWithholdingTax = computed(() => toNumber(props.invoice.withholding_tax_amount) > 0);

const sellerInitials = computed(() => {
    return (props.invoice.seller.trade_name ?? props.invoice.seller.registered_name)
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((word) => word[0])
        .join('')
        .toUpperCase();
});

//Notification message for successful email sending
const mailing = ref(false);
const successMessage = ref('');

const mailInvoice = () => {
    if (!getBuyerEmail(props.invoice)) {
        alert('Buyer does not have an email address.');
        return;
    }

    if (!confirm(`Send invoice ${props.invoice.invoice_number} to ${getBuyerEmail(props.invoice)}?`)) return;

    mailing.value = true;
    successMessage.value = '';

    router.post(`/invoice/${props.invoice.id}/mail`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            successMessage.value = 'Invoice email sent successfully.';

            setTimeout(() => {
                successMessage.value = '';
            }, 5000);
        },
        onFinish: () => mailing.value = false,
    });
};

// Download PDF from server (no browser header/footer)
const downloadPdf = () => {
    window.location.href = `/invoice/${props.invoice.id}/pdf/download`;
};

// Print invoice (browser print dialog)
const printInvoice = () => {
    window.print();
};

// Go back to invoice list
const goBack = () => {
    router.visit('/invoice');
};


</script>

<template>
    <div class="min-h-screen bg-gray-200 py-6 print:bg-white print:py-0">

        <!-- Notification message -->
        <div
            v-if="successMessage"
            class="mx-auto mb-4 flex max-w-[816px] items-center gap-3 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 print:hidden"
        >
            <CheckCircle2 class="h-5 w-5 shrink-0 text-green-600" />
            <span>{{ successMessage }}</span>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="mx-auto mb-4 flex max-w-[816px] justify-end gap-2 print:hidden">
            <button type="button" @click="goBack" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Back
            </button>
            <button type="button" @click="downloadPdf" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                Download PDF
            </button>
            <a
                :href="`/invoice/${invoice.id}/pdf`"
                target="_blank"
                class="rounded bg-black px-3 py-2 text-sm text-white"
            >
                View PDF
            </a>
            <button type="button" @click="mailInvoice" :disabled="mailing || !getBuyerEmail(invoice)" class="rounded bg-blue-600 px-3 py-2 text-sm text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50" >
            {{ mailing ? 'Sending...' : 'Mail' }}
            </button>
        </div>

        <!-- SHORT BOND PAPER INVOICE (8.5 x 11 in) -->
        <div class="mx-auto min-h-[1056px] w-[816px] bg-white px-10 py-8 shadow-lg print:mx-0 print:min-h-0 print:w-full print:shadow-none">
            <!-- HEADER -->
            <div class="flex items-start justify-between">
                <!-- COMPANY INFORMATION -->
                <div class="flex items-start gap-3">
                    <!-- LOGO (falls back to initials if seller has no logo) -->
                    <div class="flex h-[51px] w-[51px] shrink-0 items-center justify-center overflow-hidden border bg-white text-center text-[14px] font-bold leading-tight text-black">
                        <img
                            v-if="invoice.seller.logo"
                            :src="`/storage/${invoice.seller.logo}`"
                            alt="Company Logo"
                            class="h-full w-full object-contain"
                        />
                        <template v-else>{{ sellerInitials }}</template>
                    </div>
                    <!-- CO             MPANY DETAILS -->
                    <div class="leading-tight">
                        <div class="text-[17px] font-bold text-black">{{ invoice.seller.trade_name ?? invoice.seller.registered_name }}</div>
                        <div class="text-[9px] text-black">TIN: <strong>{{ invoice.seller.tin ?? '-' }}</strong></div>
                        <div v-if="invoice.seller.address_line_1" class="text-[9px]">{{ invoice.seller.address_line_1 }}</div>
                        <div v-if="invoice.seller.address_line_2" class="text-[9px]">{{ invoice.seller.address_line_2 }}</div>
                        <div class="text-[9px]">
                            <span v-if="invoice.seller.barangay">Brgy. {{ invoice.seller.barangay }}, </span>
                            <span v-if="invoice.seller.city">{{ invoice.seller.city }}</span>
                            <span v-if="invoice.seller.province">, {{ invoice.seller.province }}</span>
                            <span v-if="invoice.seller.postal_code"> {{ invoice.seller.postal_code }}</span>
                            <span v-if="invoice.seller.country_code">, {{ invoice.seller.country_code }}</span>
                        </div>
                    </div>
                </div>
                <!-- INVOICE TITLE -->
                <div class="text-right">
                    <h1 class="text-[17px] font-bold">{{ invoice.document_type ?? 'Service Invoice' }}</h1>
                </div>
            </div>

            <!-- INVOICE NUMBER -->
            <div class="mt-10 flex justify-end">
                <span class="text-[10px] font-medium text-red-600">Invoice No: {{ invoice.invoice_number }}</span>
            </div>

            <!-- SALES TYPE AND DATE -->
            <div class="mt-4 flex items-center justify-between text-[9px]">
                <div class="font-medium uppercase">{{ invoice.document_type ?? '-' }}</div>
                <div>Date: {{ formatDate(invoice.invoice_date) }}</div>
            </div>

            <!-- BILLED TO -->
            <div class="mt-4 border border-black print-avoid-break">
                <div class="border-b border-black px-2 py-1 text-[10px] font-bold">BILLED TO:</div>

                <div class="grid grid-cols-[105px_1fr_95px_105px] px-2 py-1 text-[9px]">
                    <div class="font-bold">Registered Name:</div>
                    <div>{{ getBuyerName(invoice) }}</div>
                    <div></div>
                    <div></div>
                </div>

                <div class="grid grid-cols-[105px_1fr_95px_105px] px-2 py-1 text-[9px]">
                    <div class="font-bold">PO Number:</div>
                    <div>{{ invoice.purchase_order_number ?? '—' }}</div>
                    <div></div>
                    <div></div>
                </div>

                <div class="grid grid-cols-[105px_1fr_95px_105px] px-2 py-1 text-[9px]">
                    <div class="font-bold">Reference No:</div>
                    <div class="col-span-3">
                        {{ invoice.reference_number ?? '—' }}
                        <span v-if="getBuyerTradeName(invoice)">({{ getBuyerTradeName(invoice) }})</span>
                    </div>
                </div>

                <div class="grid grid-cols-[105px_1fr_95px_105px] px-2 py-1 text-[9px]">
                    <div class="font-bold">TIN:</div>
                    <div class="col-span-3">{{ getBuyerTin(invoice) }}</div>
                </div>

                <div class="grid grid-cols-[105px_1fr_95px_105px] px-2 py-1 text-[9px]">
                    <div class="font-bold">Address:</div>
                    <div class="col-span-3 leading-tight">
                        {{ getBuyerAddress1(invoice) }}<span v-if="getBuyerAddress2(invoice)">, {{ getBuyerAddress2(invoice) }}</span><span v-if="getBuyerBarangay(invoice)">, Brgy. {{ getBuyerBarangay(invoice) }}</span><span v-if="getBuyerCity(invoice)">, {{ getBuyerCity(invoice) }}</span><span v-if="getBuyerProvince(invoice)">, {{ getBuyerProvince(invoice) }}</span><span v-if="getBuyerPostalCode(invoice)"> {{ getBuyerPostalCode(invoice) }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-[105px_1fr_95px_105px] px-2 py-1 text-[9px]">
                    <div class="font-bold">Customer Code:</div>
                    <div class="col-span-3">{{ getBuyerCustomerCode(invoice) }}</div>
                </div>

                <div class="grid grid-cols-[105px_1fr_95px_105px] px-2 py-1 text-[9px]">
                    <div class="font-bold">Nationality:</div>
                    <div class="col-span-3">{{ getBuyerCountry(invoice) }}</div>
                </div>

                <div class="grid grid-cols-[105px_1fr_95px_105px] px-2 py-1 text-[9px]">
                    <div class="font-bold">Currency:</div>
                    <div>{{ invoice.currency_code }}</div>
                    <div class="font-bold">Due Date:</div>
                    <div>{{ formatDate(invoice.due_date) }}</div>
                </div>
            </div>

            <!-- ITEMS -->
            <table class="mt-3 w-full border-collapse border border-black text-[9px]">
                <thead>
                    <tr>
                        <th class="w-[44%] border border-black px-2 py-1.5 text-center">Item Description / Nature of Service</th>
                        <th class="w-[10%] border border-black px-2 py-1.5 text-center">Qty</th>
                        <th class="w-[18%] border border-black px-2 py-1.5 text-center">Unit Cost/Price</th>
                        <th class="w-[18%] border border-black px-2 py-1.5 text-center">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in invoice.items" :key="item.id">
                        <td class="border border-black px-2 py-1.5">
                            {{ item.description }}
                            <span v-if="item.item_code" class="text-gray-500">({{ item.item_code }})</span>
                        </td>
                        <td class="border border-black px-2 py-1.5 text-center">
                            {{ toNumber(item.quantity) }}
                            <span v-if="item.unit_code">{{ item.unit_code }}</span>
                        </td>
                        <td class="border border-black px-2 py-1.5 text-right">{{ invoice.currency_code }} {{ formatMoney(item.unit_price) }}</td>
                        <td class="border border-black px-2 py-1.5 text-right">{{ invoice.currency_code }} {{ formatMoney(item.line_total ?? (toNumber(item.quantity) * toNumber(item.unit_price))) }}</td>
                    </tr>
                    <tr v-if="!invoice.items.length">
                        <td colspan="4" class="border border-black px-2 py-3 text-center text-gray-400">No line items</td>
                    </tr>
                </tbody>
            </table>

            <!---CONTACT-->
            <div v-if="getBuyerEmail(invoice)" class="mt-3 w-[25%]">
                <div class="min-h-[30px] border border-black px-2 py-2 text-[9px]">
                    <span class="font-bold">Buyer's Email: </span>{{ getBuyerEmail(invoice) }}
                </div>
            </div>

            <div v-if="getBuyerPhone(invoice)" class="w-[25%]">
                <div class="min-h-[30px] border border-black px-2 py-2 text-[9px]">
                    <span class="font-bold">Buyer's Phone No: </span>{{ getBuyerPhone(invoice) }}
                </div>
            </div>

            <!-- TOTALS -->
            <div class="mt-7 flex justify-end print-avoid-break">
                <table class="w-[240px] border-collapse border border-black text-[9px]">
                    <tbody>
                        <!-- GROSS / TOTAL SALES -->
                        <tr>
                            <td class="border border-black px-2 py-1.5 text-right font-bold">Total Sales</td>
                            <td class="border border-black px-2 py-1.5 text-right">{{ invoice.currency_code }} {{ formatMoney(invoice.gross_amount) }}</td>
                        </tr>
                        <!-- DISCOUNT -->
                        <tr>
                            <td class="border border-black px-2 py-1.5 text-right">
                                <div>Less: Discount</div>
                            </td>
                            <td class="border border-black px-2 py-1.5 text-right">{{ invoice.currency_code }} {{ formatMoney(invoice.discount_amount) }}</td>
                        </tr>

                        <!--     WITHHOLDING TAX -->
                        <tr>
                            <td class="border border-black px-2 py-1.5 text-right">Less: Withholding Tax</td>
                            <td class="border border-black px-2 py-1.5 text-right">{{ invoice.currency_code }} {{ formatMoney(invoice.withholding_tax_amount) }}</td>
                        </tr>
                        <!-- VAT -->
                        <tr>
                            <td class="border   border-black px-2 py-1.5 text-right">Add: VAT</td>
                            <td class="border border-black px-2 py-1.5 text-right">{{ invoice.currency_code }} {{ formatMoney(invoice.vat_amount) }}</td>
                        </tr>
                        <!-- TOTAL AMOUNT DUE -->
                        <tr>
                            <td class="border border-black px-2 py-1.5 text-right font-bold">TOTAL AMOUNT DUE</td>
                            <td class="border border-black px-2 py-1.5 text-right font-bold">{{ invoice.currency_code }} {{ formatMoney(invoice.amount_due) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- TAX NOTICE -->
            <div class="mt-10 text-center text-[8px] font-bold">"THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAX."</div>
        </div>
    </div>
</template>

<style scoped>
@page {
    size: 8.5in 11in;
    margin: 0;
}

@media print {
    :deep(*) {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        color-adjust: exact;
    }

    table, tr, thead {
        break-inside: avoid;
    }

    .print-avoid-break {
        break-inside: avoid;
    }
}
</style>
