<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Building2, CheckCircle2, Pencil } from '@lucide/vue';
import { onMounted, ref } from 'vue';

interface Seller {
    id: number;
    user_id: number;
    tin: string;
    registered_name: string;
    trade_name: string | null;
    branch_code: string | null;
    address_line_1: string;
    address_line_2: string | null;
    barangay: string;
    city: string;
    province: string;
    postal_code: string;
    country_code: string;
    company_email: string;
    phone: string;
    logo: string | null;
}

interface Props {
    seller: Seller;
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

const goToEdit = () => router.get('/seller/edit');
</script>

<template>
    <Head title="Seller Information" />

    <div class="space-y-6">
        <div
            v-if="successMessage"
            class="flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
        >
            <CheckCircle2 class="h-5 w-5 shrink-0 text-green-600" />
            <span>{{ successMessage }}</span>
        </div>
        <div class="flex items-start justify-between">
            <div>
                <h1 class="flex items-center gap-2 text-2xl font-semibold text-gray-900">
                    <Building2 class="h-6 w-6" /> Seller Information
                </h1>
                <p class="mt-1 text-sm text-gray-500">View your company information used for invoices and business documents.</p>
            </div>
            <button type="button" @click="goToEdit" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800">
                <Pencil class="h-4 w-4" /> Edit Seller
            </button>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900">Business Information</h2>
                <p class="mt-1 text-sm text-gray-500">Basic registered business information.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Company Logo</label>
                    <div class="flex h-32 items-center justify-center overflow-hidden rounded-lg border border-gray-300 bg-gray-50">
                        <img v-if="props.seller.logo" :src="`/storage/${props.seller.logo}`" alt="Company Logo" class="h-full w-full object-contain p-2" />
                        <div v-else class="text-sm text-gray-400">No logo uploaded</div>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">TIN</label>
                    <input :value="props.seller.tin" type="text" disabled class="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 outline-none" />
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Registered Name</label>
                    <input :value="props.seller.registered_name" type="text" disabled class="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 outline-none" />
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Trade Name</label>
                    <input :value="props.seller.trade_name || '—'" type="text" disabled class="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 outline-none" />
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Branch Code</label>
                    <input :value="props.seller.branch_code || '—'" type="text" disabled class="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 outline-none" />
                </div>
            </div>

            <div class="border-t border-gray-200">
                <div class="border-b border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Business Address</h2>
                    <p class="mt-1 text-sm text-gray-500">Address that will appear on your invoices.</p>
                </div>

                <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="lg:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Address Line 1</label>
                        <input :value="props.seller.address_line_1" type="text" disabled class="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 outline-none" />
                    </div>

                    <div class="lg:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Address Line 2</label>
                        <input :value="props.seller.address_line_2 || '—'" type="text" disabled class="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 outline-none" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Barangay</label>
                        <input :value="props.seller.barangay" type="text" disabled class="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 outline-none" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">City</label>
                        <input :value="props.seller.city" type="text" disabled class="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 outline-none" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Province</label>
                        <input :value="props.seller.province" type="text" disabled class="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 outline-none" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Postal Code</label>
                        <input :value="props.seller.postal_code" type="text" disabled class="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 outline-none" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Country Code</label>
                        <input :value="props.seller.country_code" type="text" disabled class="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm uppercase text-gray-600 outline-none" />
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200">
                <div class="border-b border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900">Contact Information</h2>
                    <p class="mt-1 text-sm text-gray-500">Contact details for your business.</p>
                </div>

                <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 lg:grid-cols-4">
                    <div class="lg:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Company Email</label>
                        <input :value="props.seller.company_email" type="email" disabled class="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 outline-none" />
                    </div>

                    <div class="lg:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Phone</label>
                        <input :value="props.seller.phone" type="text" disabled class="w-full cursor-not-allowed rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 outline-none" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
