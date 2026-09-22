<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { CheckCircle2 } from '@lucide/vue';

defineOptions({
    layout: { breadcrumbs: [{ title: 'Buyer List', href: '/buyer' }] },
});

interface Buyer {
    id: number;
    tin: string | null;
    registered_name: string;
    trade_name: string | null;
    customer_code: string;
    address_line_1: string;
    barangay: string | null;
    city: string;
    province: string;
    country_code: string;
    email: string | null;
    phone: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedBuyers {
    data: Buyer[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
}

interface Props {
    buyers: PaginatedBuyers;
    filters: { search?: string };
    flash?: {
        success?: string;
        error?: string;
    };
}

const props = defineProps<Props>();

//Notification message for successful update
const successMessage = ref('');

onMounted(() => {
    if (!props.flash?.success) return;

    successMessage.value = props.flash.success;

    setTimeout(() => {
        successMessage.value = '';
    }, 5000);
});


const search = ref(props.filters.search ?? '');

const applySearch = () => router.get('/buyer', { search: search.value || undefined }, {
    preserveState: true,
    preserveScroll: true,
});

const clearFilters = () => {
    search.value = '';
    router.get('/buyer', {}, { preserveState: true, preserveScroll: true });
};

const goToPage = (url: string | null) => {
    if (!url) return;
    router.visit(url, { preserveState: true, preserveScroll: true });
};

const viewBuyer = (id: number) => router.visit(`/buyer/${id}`);
const createBuyer = () => router.visit('/buyer/create');
const editBuyer = (id: number) => router.visit(`/buyer/${id}/edit`);

const deleteBuyer = (id: number) => {
    if (!confirm('Delete this buyer and all associated invoices?')) return;

    router.delete(`/buyer/${id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="p-6">
        <div
            v-if="successMessage"
            class="mb-4 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
        >
            <CheckCircle2 class="h-5 w-5 shrink-0 text-green-600" />
            <span>{{ successMessage }}</span>
        </div>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Buyer</h1>
                <p class="mt-1 text-sm text-gray-500">Manage your buyers.</p>
            </div>
            <button type="button" @click="createBuyer" class="rounded-lg bg-blue-600 px-5 py-3 text-xs font-semibold uppercase tracking-wider text-white hover:bg-blue-700">
                Create Buyer
            </button>
        </div>

        <div class="mb-4 rounded-lg border border-gray-200 bg-white p-3">
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-medium text-gray-600">Search</label>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Registered name, customer code, TIN, or email..."
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        @keyup.enter="applySearch"
                    />
                </div>
                <button type="button" @click="applySearch" class="rounded-md bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700">Search</button>
                <button type="button" @click="clearFilters" class="rounded-md border border-gray-300 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50">Clear</button>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
            <table class="w-full table-fixed divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-[10%] px-3 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Customer Code</th>
                        <th class="w-[10%] px-3 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">TIN</th>
                        <th class="w-[14%] px-3 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Registered Name</th>
                        <th class="w-[12%] px-3 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Trade Name</th>
                        <th class="w-[10%] px-3 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Address</th>
                        <th class="w-[5%] px-3 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Country</th>
                        <th class="w-[12%] px-3 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Email</th>
                        <th class="w-[8%] px-3 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500">Phone</th>
                        <th class="w-[8%] px-3 py-3 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    <tr v-for="buyer in buyers.data" :key="buyer.id" class="transition hover:bg-gray-50">
                        <td class="truncate px-3 py-3 font-medium text-gray-900">{{ buyer.customer_code }}</td>
                        <td class="truncate px-3 py-3 text-gray-700">{{ buyer.tin || '-' }}</td>
                        <td class="truncate px-3 py-3 font-medium text-gray-900">{{ buyer.registered_name }}</td>
                        <td class="truncate px-3 py-3 text-gray-600">{{ buyer.trade_name || '-' }}</td>
                        <td class="px-3 py-3 align-top text-xs text-gray-600">
                            <div class="line-clamp-2 break-words leading-4">
                                {{ buyer.address_line_1 }}, {{ buyer.barangay ? buyer.barangay + ', ' : '' }}{{ buyer.city }}, {{ buyer.province }}
                            </div>
                        </td>
                        <td class="truncate px-3 py-3 text-gray-600">{{ buyer.country_code }}</td>
                        <td class="truncate px-3 py-3 text-gray-600">{{ buyer.email || '-' }}</td>
                        <td class="truncate px-3 py-3 text-gray-600">{{ buyer.phone || '-' }}</td>
                        <td class="px-3 py-3">
                            <div class="flex justify-center gap-1">
                                <button type="button" @click="viewBuyer(buyer.id)" class="rounded border border-gray-300 px-2 py-1 text-[10px] font-medium text-gray-700 hover:bg-gray-100">View</button>
                                <button type="button" @click="editBuyer(buyer.id)" class="rounded border border-gray-300 px-2 py-1 text-[10px] font-medium text-gray-700 hover:bg-gray-100">Edit</button>
                                <button type="button" @click="deleteBuyer(buyer.id)" class="rounded border border-red-300 px-2 py-1 text-[10px] font-medium text-red-600 hover:bg-red-50">Delete</button>
                            </div>
                        </td>
                    </tr>

                    <tr v-if="buyers.data.length === 0">
                        <td colspan="9" class="px-3 py-8 text-center text-xs text-gray-500">No buyers found.</td>
                    </tr>
                </tbody>
            </table>

            <div v-if="buyers.total > 0" class="flex items-center justify-between border-t border-gray-200 px-3 py-3">
                <p class="text-[10px] text-gray-500">
                    Showing
                    <span class="font-medium text-gray-700">{{ buyers.from }}</span>
                    -
                    <span class="font-medium text-gray-700">{{ buyers.to }}</span>
                    of
                    <span class="font-medium text-gray-700">{{ buyers.total }}</span>
                    buyers
                </p>

                <div class="flex items-center gap-1">
                    <button
                        v-for="link in buyers.links"
                        :key="link.label"
                        type="button"
                        :disabled="!link.url"
                        @click="goToPage(link.url)"
                        :class="[
                            'rounded border px-2 py-1 text-[10px] font-medium',
                            link.active ? 'border-blue-600 bg-blue-600 text-white' : 'border-gray-300 text-gray-600 hover:bg-gray-50',
                            !link.url ? 'cursor-not-allowed opacity-40' : ''
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
