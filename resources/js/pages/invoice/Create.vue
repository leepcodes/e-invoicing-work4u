<script setup lang="ts">
import { computed, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

interface Buyer {
    id: number;
    registered_name: string;
    trade_name: string | null;
    customer_code: string | null;
}

interface ItemMaster {
    id: number;
    seller_id: number;
    item_code: string;
    description: string;
    unit_code: string;
    unit_price: string | number;
}

interface InvoiceItem {
    item_id: string;
    item_code: string;
    description: string;
    quantity: number;
    unit_code: string;
    unit_price: number;
    discount_amount: number;
    tax_type: string;
    tax_category: string;
    tax_rate: number;
}

interface Props {
    buyers: Buyer[];
    items: ItemMaster[];
    invoiceNumber: string;
    invoiceDate: string;
    invoiceTime: string;
    exchangeRateDate: string;
}

const props = withDefaults(defineProps<Props>(), {
    buyers: () => [],
    items: () => [],
});

const input = 'w-full rounded-md border border-gray-300 px-3 py-2 text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500';
const readonlyInput = 'w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-xs';
const label = 'mb-1 block text-xs font-medium text-gray-600';
const section = 'rounded-lg border border-gray-200 bg-white';
const header = 'border-b border-gray-200 px-4 py-3';
const button = 'rounded-md px-3 py-2 text-xs font-medium';

const back = () => router.visit('/invoice');

const createItem = (): InvoiceItem => ({
    item_id: '',
    item_code: '',
    description: '',
    quantity: 0,
    unit_code: '',
    unit_price: 0,
    discount_amount: 0,
    tax_type: '',
    tax_category: '',
    tax_rate: 0,
});

const form = useForm({
    customer_type: 'existing',
    buyer_id: '',
    buyer_name: '',
    buyer_tin: '',
    buyer_trade_name: '',
    buyer_address_line_1: '',
    buyer_address_line_2: '',
    buyer_barangay: '',
    buyer_city: '',
    buyer_province: '',
    buyer_postal_code: '',
    buyer_country_code: 'PH',
    buyer_email: '',
    buyer_phone: '',
    document_type: '',
    invoice_number: props.invoiceNumber,
    invoice_date: '',
    invoice_time: '',
    due_date: '',
    reference_number: '',
    purchase_order_number: '',
    currency_code: '',
    accounting_currency_code: '',
    exchange_rate: 0,
    exchange_rate_date: '',
    exchange_rate_source: '',
    amount_paid: 0,
    payment_terms: '',
    payment_method: '',
    payment_status: '',
    items: [createItem()],
});

const selectedBuyer = computed(() => props.buyers.find(b => b.id === Number(form.buyer_id)));

watch(() => form.customer_type, type => {
    if (type === 'one_time') {
        form.buyer_id = '';
    } else {
        [
            'buyer_name',
            'buyer_tin',
            'buyer_trade_name',
            'buyer_address_line_1',
            'buyer_address_line_2',
            'buyer_barangay',
            'buyer_city',
            'buyer_province',
            'buyer_postal_code',
            'buyer_email',
            'buyer_phone',
        ].forEach(field => form[field as keyof typeof form] = '' as never);
    }
});

watch(() => form.items.map(item => item.tax_type), () => {
    form.items.forEach(item => {
        if (item.tax_type === 'NONE') item.tax_rate = 0;
    });
}, { deep: true });

const selectItem = (item: InvoiceItem) => {
    const selected = props.items.find(i => i.id === Number(item.item_id));

    if (!selected) {
        item.item_id = '';
        item.item_code = '';
        item.description = '';
        item.unit_code = '';
        item.unit_price = 0;
        return;
    }

    item.item_code = selected.item_code;
    item.description = selected.description;
    item.unit_code = selected.unit_code;
    item.unit_price = Number(selected.unit_price);
};

const calculateItem = (item: InvoiceItem) => {
    const quantity = Number(item.quantity || 0);
    const price = Number(item.unit_price || 0);
    const discount = Number(item.discount_amount || 0);
    const rate = Number(item.tax_rate || 0);
    const gross = quantity * price;
    const net = Math.max(gross - discount, 0);
    const taxable = item.tax_type === 'VAT' ? net : 0;
    const tax = taxable * rate / 100;

    return {
        gross: +gross.toFixed(2),
        discount: +discount.toFixed(2),
        net: +net.toFixed(2),
        taxable: +taxable.toFixed(2),
        tax: +tax.toFixed(2),
        total: +(net + tax).toFixed(2),
    };
};

const invoiceTotals = computed(() => form.items.reduce((totals, item) => {
    const x = calculateItem(item);
    totals.gross += x.gross;
    totals.discount += x.discount;
    totals.taxable += x.taxable;
    totals.vat += x.tax;
    totals.net += x.net;
    totals.total += x.total;
    return totals;
}, {
    gross: 0,
    discount: 0,
    taxable: 0,
    vat: 0,
    net: 0,
    total: 0,
}));

const totals = computed(() => ({
    ...invoiceTotals.value,
    amountDue: Math.max(invoiceTotals.value.total - Number(form.amount_paid || 0), 0),
}));

const accountingTotals = computed(() => {
    const rate = Number(form.exchange_rate || 1);

    return {
        gross: +(totals.value.gross * rate).toFixed(2),
        taxable: +(totals.value.taxable * rate).toFixed(2),
        vat: +(totals.value.vat * rate).toFixed(2),
        total: +(totals.value.total * rate).toFixed(2),
        amountDue: +(totals.value.amountDue * rate).toFixed(2),
    };
});

const addItem = () => form.items.push(createItem());

const removeItem = (index: number) => {
    if (form.items.length > 1) form.items.splice(index, 1);
};

const submit = () => {
    form.post('/invoice', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const documentTypes = [
    ['INVOICE', 'Invoice'],
    ['CREDIT_NOTE', 'Credit Note'],
    ['DEBIT_NOTE', 'Debit Note'],
];

const currencies = ['PHP', 'USD', 'EUR', 'SGD', 'JPY'];
const accountingCurrencies = ['PHP', 'USD'];
const exchangeRateSources = ['BSP', 'MANUAL'];
const units = ['EA', 'PCS', 'HR', 'DAY', 'KG', 'SET', 'BOX', 'G', 'L', 'ML', 'M'];

const taxTypes = [
    ['VAT', 'VAT'],
    ['NONE', 'None'],
];

const taxCategories = [
    ['STANDARD', 'Standard'],
    ['ZERO_RATED', 'Zero Rated'],
    ['EXEMPT', 'Exempt'],
];

const paymentTerms = ['COD', '15 DAYS', '30 DAYS', '60 DAYS'];

const paymentMethods = [
    ['BANK_TRANSFER', 'Bank Transfer'],
    ['CASH', 'Cash'],
    ['CHECK', 'Check'],
];
</script>

<template>
    <div class="p-6 print:hidden">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Create Invoice</h1>
                <p class="mt-1 text-sm text-gray-500">Create a new invoice.</p>
            </div>
            <button type="button" @click="back" :class="[button, 'border border-gray-300 text-gray-600 hover:bg-gray-50']">Back</button>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div v-if="Object.keys(form.errors).length" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">Please fix the following:</p>
                <ul class="mt-1 list-disc pl-5">
                    <li v-for="(message, field) in form.errors" :key="field">{{ message }}</li>
                </ul>
            </div>

            <section :class="section">
                <div :class="header">
                    <h2 class="text-sm font-semibold text-gray-900">Document Information</h2>
                </div>
                <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 lg:grid-cols-5">
                    <div>
                        <label :class="label">Document Type</label>
                        <select v-model="form.document_type" :class="input">
                            <option value="">Select Document Type</option>
                            <option v-for="[value, text] in documentTypes" :key="value" :value="value">{{ text }}</option>
                        </select>
                    </div>
                    <div>
                        <label :class="label">Invoice Number</label>
                        <input v-model="form.invoice_number" readonly :class="readonlyInput">
                    </div>
                    <div>
                        <label :class="label">Invoice Date</label>
                        <input v-model="form.invoice_date" type="date" :class="input">
                    </div>
                    <div>
                        <label :class="label">Invoice Time</label>
                        <input v-model="form.invoice_time" type="time" :class="input">
                    </div>
                    <div>
                        <label :class="label">Due Date</label>
                        <input v-model="form.due_date" type="date" :class="input">
                    </div>
                    <div class="sm:col-span-2">
                        <label :class="label">Reference Number</label>
                        <input v-model="form.reference_number" :class="input">
                    </div>
                    <div class="sm:col-span-2">
                        <label :class="label">Purchase Order Number</label>
                        <input v-model="form.purchase_order_number" :class="input">
                    </div>
                </div>
            </section>

            <section :class="section">
                <div :class="header">
                    <h2 class="text-sm font-semibold text-gray-900">Customer</h2>
                </div>
                <div class="space-y-3 p-4">
                    <div class="max-w-xs">
                        <label :class="label">Customer Type</label>
                        <select v-model="form.customer_type" :class="input">
                            <option value="existing">Existing Customer</option>
                            <option value="one_time">One-time Customer</option>
                        </select>
                    </div>

                    <template v-if="form.customer_type === 'existing'">
                        <div class="grid grid-cols-1 gap-3 lg:grid-cols-4">
                            <div class="lg:col-span-2">
                                <label :class="label">Customer</label>
                                <select v-model="form.buyer_id" :class="input">
                                    <option value="">Select Customer</option>
                                    <option v-for="buyer in props.buyers" :key="buyer.id" :value="String(buyer.id)">{{ buyer.registered_name }}</option>
                                </select>
                            </div>
                            <div>
                                <label :class="label">Customer Code</label>
                                <input :value="selectedBuyer?.customer_code ?? ''" readonly :class="readonlyInput">
                            </div>
                            <div>
                                <label :class="label">Trade Name</label>
                                <input :value="selectedBuyer?.trade_name ?? ''" readonly :class="readonlyInput">
                            </div>
                        </div>
                        <p v-if="!props.buyers.length" class="text-xs text-red-500">No customers found for this seller.</p>
                    </template>

                    <div v-else class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="lg:col-span-2">
                            <label :class="label">Customer Name</label>
                            <input v-model="form.buyer_name" placeholder="Customer name" :class="input">
                        </div>
                        <div>
                            <label :class="label">TIN</label>
                            <input v-model="form.buyer_tin" placeholder="TIN" :class="input">
                        </div>
                        <div>
                            <label :class="label">Trade Name</label>
                            <input v-model="form.buyer_trade_name" placeholder="Trade name" :class="input">
                        </div>
                        <div class="lg:col-span-2">
                            <label :class="label">Address Line 1</label>
                            <input v-model="form.buyer_address_line_1" :class="input">
                        </div>
                        <div>
                            <label :class="label">Address Line 2</label>
                            <input v-model="form.buyer_address_line_2" :class="input">
                        </div>
                        <div>
                            <label :class="label">Barangay</label>
                            <input v-model="form.buyer_barangay" :class="input">
                        </div>
                        <div>
                            <label :class="label">City</label>
                            <input v-model="form.buyer_city" :class="input">
                        </div>
                        <div>
                            <label :class="label">Province</label>
                            <input v-model="form.buyer_province" :class="input">
                        </div>
                        <div>
                            <label :class="label">Postal Code</label>
                            <input v-model="form.buyer_postal_code" :class="input">
                        </div>
                        <div>
                            <label :class="label">Email</label>
                            <input v-model="form.buyer_email" type="email" :class="input">
                        </div>
                        <div>
                            <label :class="label">Phone</label>
                            <input v-model="form.buyer_phone" :class="input">
                        </div>
                    </div>
                </div>
            </section>

            <section :class="section">
                <div :class="header">
                    <h2 class="text-sm font-semibold text-gray-900">Currency</h2>
                </div>
                <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 lg:grid-cols-5">
                    <div>
                        <label :class="label">Currency</label>
                        <select v-model="form.currency_code" :class="input">
                            <option value="">Select Currency</option>
                            <option v-for="currency in currencies" :key="currency" :value="currency">{{ currency }}</option>
                        </select>
                    </div>
                    <div>
                        <label :class="label">Accounting Currency</label>
                        <select v-model="form.accounting_currency_code" :class="input">
                            <option value="">Select Accounting Currency</option>
                            <option v-for="currency in accountingCurrencies" :key="currency" :value="currency">{{ currency }}</option>
                        </select>
                    </div>
                    <div>
                        <label :class="label">Exchange Rate</label>
                        <input v-model.number="form.exchange_rate" type="number" min="0" step="0.00000001" :class="input">
                    </div>
                    <div>
                        <label :class="label">Rate Date</label>
                        <input v-model="form.exchange_rate_date" type="date" :class="input">
                    </div>
                    <div>
                        <label :class="label">Rate Source</label>
                        <select v-model="form.exchange_rate_source" :class="input">
                            <option value="">Select Rate Source</option>
                            <option v-for="source in exchangeRateSources" :key="source" :value="source">{{ source }}</option>
                        </select>
                    </div>
                </div>
            </section>

            <section :class="section">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Invoice Items</h2>
                        <p class="mt-1 text-xs text-gray-500">Select an existing item or enter a manual item.</p>
                    </div>
                    <button type="button" @click="addItem" :class="[button, 'bg-blue-600 text-white hover:bg-blue-700']">+ Add Item</button>
                </div>

                <div class="space-y-3 p-4">
                    <div v-for="(item, index) in form.items" :key="index" class="overflow-hidden rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-3 py-2">
                            <h3 class="text-xs font-semibold text-gray-700">Item #{{ index + 1 }}</h3>
                            <button type="button" @click="removeItem(index)" :disabled="form.items.length === 1" class="text-xs font-medium text-red-600 hover:text-red-800 disabled:text-gray-400">Remove</button>
                        </div>

                        <div class="p-3">
                            <div class="mb-3 grid grid-cols-1 gap-3 lg:grid-cols-5">
                                <div class="lg:col-span-2">
                                    <label :class="label">Item</label>
                                    <select v-model="item.item_id" @change="selectItem(item)" :class="input">
                                        <option value="">Manual Item</option>
                                        <option v-for="master in props.items" :key="master.id" :value="String(master.id)">
                                            {{ master.item_code }} - {{ master.description }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label :class="label">Item Code</label>
                                    <input v-model="item.item_code" :readonly="!!item.item_id" :class="item.item_id ? readonlyInput : input">
                                </div>
                                <div>
                                    <label :class="label">Unit</label>
                                    <select v-model="item.unit_code" :disabled="!!item.item_id" :class="item.item_id ? readonlyInput : input">
                                        <option value="">Select Unit</option>
                                        <option v-for="unit in units" :key="unit" :value="unit">{{ unit }}</option>
                                    </select>
                                </div>
                                <div class="lg:col-span-5">
                                    <label :class="label">Description</label>
                                    <input v-model="item.description" :readonly="!!item.item_id" :class="item.item_id ? readonlyInput : input">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
                                <div>
                                    <label :class="label">Quantity</label>
                                    <input v-model.number="item.quantity" type="number" min="0.01" step="0.01" :class="input">
                                </div>
                                <div>
                                    <label :class="label">Unit Price</label>
                                    <input v-model.number="item.unit_price" type="number" min="0" step="0.01" :class="input">
                                </div>
                                <div>
                                    <label :class="label">Gross Amount</label>
                                    <input :value="calculateItem(item).gross.toFixed(2)" readonly :class="readonlyInput">
                                </div>
                                <div>
                                    <label :class="label">Discount</label>
                                    <input v-model.number="item.discount_amount" type="number" min="0" step="0.01" :class="input">
                                </div>
                                <div>
                                    <label :class="label">Net Amount</label>
                                    <input :value="calculateItem(item).net.toFixed(2)" readonly :class="readonlyInput">
                                </div>
                                <div>
                                    <label :class="label">Tax Type</label>
                                    <select v-model="item.tax_type" :class="input">
                                        <option value="">Select Tax Type</option>
                                        <option v-for="[value, text] in taxTypes" :key="value" :value="value">{{ text }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label :class="label">Tax Category</label>
                                    <select v-model="item.tax_category" :class="input">
                                        <option value="">Select Tax Category</option>
                                        <option v-for="[value, text] in taxCategories" :key="value" :value="value">{{ text }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label :class="label">Tax Rate</label>
                                    <input v-model.number="item.tax_rate" type="number" min="0" step="0.01" :disabled="item.tax_type === 'NONE'" :class="input">
                                </div>
                                <div>
                                    <label :class="label">Taxable Amount</label>
                                    <input :value="calculateItem(item).taxable.toFixed(2)" readonly :class="readonlyInput">
                                </div>
                                <div>
                                    <label :class="label">Tax Amount</label>
                                    <input :value="calculateItem(item).tax.toFixed(2)" readonly :class="readonlyInput">
                                </div>
                                <div>
                                    <label :class="label">Line Total</label>
                                    <input :value="calculateItem(item).total.toFixed(2)" readonly :class="`${readonlyInput} font-semibold`">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section :class="section">
                <div :class="header">
                    <h2 class="text-sm font-semibold text-gray-900">Invoice Totals</h2>
                </div>
                <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 lg:grid-cols-5">
                    <div>
                        <label :class="label">Gross Amount</label>
                        <input :value="totals.gross.toFixed(2)" readonly :class="readonlyInput">
                    </div>
                    <div>
                        <label :class="label">Discount</label>
                        <input :value="totals.discount.toFixed(2)" readonly :class="readonlyInput">
                    </div>
                    <div>
                        <label :class="label">Taxable Amount</label>
                        <input :value="totals.taxable.toFixed(2)" readonly :class="readonlyInput">
                    </div>
                    <div>
                        <label :class="label">VAT Amount</label>
                        <input :value="totals.vat.toFixed(2)" readonly :class="readonlyInput">
                    </div>
                    <div>
                        <label :class="label">Withholding Tax</label>
                        <input value="0.00" readonly :class="readonlyInput">
                    </div>
                    <div>
                        <label :class="label">Other Tax</label>
                        <input value="0.00" readonly :class="readonlyInput">
                    </div>
                    <div>
                        <label :class="label">Net Amount</label>
                        <input :value="totals.net.toFixed(2)" readonly :class="readonlyInput">
                    </div>
                    <div>
                        <label :class="label">Total Amount</label>
                        <input :value="totals.total.toFixed(2)" readonly :class="readonlyInput">
                    </div>
                    <div>
                        <label :class="label">Amount Paid</label>
                        <input v-model.number="form.amount_paid" type="number" min="0" step="0.01" :class="input">
                    </div>
                    <div>
                        <label :class="label">Amount Due</label>
                        <input :value="totals.amountDue.toFixed(2)" readonly :class="`${readonlyInput} font-semibold`">
                    </div>
                </div>
            </section>

            <section :class="section">
                <div :class="header">
                    <h2 class="text-sm font-semibold text-gray-900">Accounting Totals</h2>
                </div>
                <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 lg:grid-cols-5">
                    <div>
                        <label :class="label">Gross Amount</label>
                        <input :value="accountingTotals.gross.toFixed(2)" readonly :class="readonlyInput">
                    </div>
                    <div>
                        <label :class="label">Taxable Amount</label>
                        <input :value="accountingTotals.taxable.toFixed(2)" readonly :class="readonlyInput">
                    </div>
                    <div>
                        <label :class="label">VAT Amount</label>
                        <input :value="accountingTotals.vat.toFixed(2)" readonly :class="readonlyInput">
                    </div>
                    <div>
                        <label :class="label">Total Amount</label>
                        <input :value="accountingTotals.total.toFixed(2)" readonly :class="readonlyInput">
                    </div>
                    <div>
                        <label :class="label">Amount Due</label>
                        <input :value="accountingTotals.amountDue.toFixed(2)" readonly :class="`${readonlyInput} font-semibold`">
                    </div>
                </div>
            </section>

            <section :class="section">
                <div :class="header">
                    <h2 class="text-sm font-semibold text-gray-900">Payment</h2>
                </div>
                <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label :class="label">Payment Terms</label>
                        <select v-model="form.payment_terms" :class="input">
                            <option value="">Select Payment Terms</option>
                            <option v-for="term in paymentTerms" :key="term" :value="term">{{ term }}</option>
                        </select>
                    </div>
                    <div>
                        <label :class="label">Payment Method</label>
                        <select v-model="form.payment_method" :class="input">
                            <option value="">Select Payment Method</option>
                            <option v-for="[value, text] in paymentMethods" :key="value" :value="value">{{ text }}</option>
                        </select>
                    </div>
                </div>
            </section>

            <div class="flex justify-end gap-2">
                <button type="button" @click="back" :class="[button, 'border border-gray-300 text-gray-600 hover:bg-gray-50']">Cancel</button>
                <button type="submit" :disabled="form.processing" :class="[button, 'bg-blue-600 text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50']">
                    {{ form.processing ? 'Creating...' : 'Create Invoice' }}
                </button>
            </div>
        </form>
    </div>
</template>
