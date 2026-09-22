<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Building2, Upload } from '@lucide/vue';
import { ref } from 'vue';

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

const props = defineProps<{ seller: Seller }>();
const logoPreview = ref<string | null>(null);

const form = useForm({
    tin: props.seller.tin ?? '',
    registered_name: props.seller.registered_name ?? '',
    trade_name: props.seller.trade_name ?? '',
    branch_code: props.seller.branch_code ?? '',
    address_line_1: props.seller.address_line_1 ?? '',
    address_line_2: props.seller.address_line_2 ?? '',
    barangay: props.seller.barangay ?? '',
    city: props.seller.city ?? '',
    province: props.seller.province ?? '',
    postal_code: props.seller.postal_code ?? '',
    country_code: props.seller.country_code ?? 'PH',
    company_email: props.seller.company_email ?? '',
    phone: props.seller.phone ?? '',
    logo: null as File | null,
});

const handleLogo = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;
    form.logo = file;
    logoPreview.value = URL.createObjectURL(file);
};

const submit = () => form.transform(data => ({ ...data, _method: 'put' })).post('/seller');
</script>

<template>
    <Head title="Seller Information" />

    <div class="space-y-6">
        <div>
            <h1 class="flex items-center gap-2 text-2xl font-semibold text-gray-900">
                <Building2 class="h-6 w-6" /> Seller Information
            </h1>
            <p class="mt-1 text-sm text-gray-500">Manage your company information used for invoices and business documents.</p>
        </div>

        <form @submit.prevent="submit" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900">Business Information</h2>
                <p class="mt-1 text-sm text-gray-500">Basic registered business information.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Company Logo</label>
                    <label class="flex h-32 cursor-pointer items-center justify-center overflow-hidden rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 transition hover:border-gray-500">
                        <img v-if="logoPreview || props.seller.logo" :src="logoPreview || `/storage/${props.seller.logo}`" alt="Company Logo" class="h-full w-full object-contain p-2" />
                        <div v-else class="flex flex-col items-center text-gray-500">
                            <Upload class="mb-2 h-6 w-6" />
                            <span class="text-xs">Upload Logo</span>
                        </div>
                        <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleLogo" />
                    </label>
                    <p class="mt-1 text-xs text-gray-500">JPG, PNG or WEBP. Max 2MB.</p>
                    <p v-if="form.errors.logo" class="mt-1 text-xs text-red-600">{{ form.errors.logo }}</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">TIN</label>
                    <input v-model="form.tin" type="text" placeholder="123-456-789-000" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900" />
                    <p v-if="form.errors.tin" class="mt-1 text-xs text-red-600">{{ form.errors.tin }}</p>
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Registered Name</label>
                    <input v-model="form.registered_name" type="text" placeholder="ABC Corporation" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900" />
                    <p v-if="form.errors.registered_name" class="mt-1 text-xs text-red-600">{{ form.errors.registered_name }}</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Trade Name</label>
                    <input v-model="form.trade_name" type="text" placeholder="ABC" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900" />
                    <p v-if="form.errors.trade_name" class="mt-1 text-xs text-red-600">{{ form.errors.trade_name }}</p>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Branch Code</label>
                    <input v-model="form.branch_code" type="text" placeholder="000" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900" />
                    <p v-if="form.errors.branch_code" class="mt-1 text-xs text-red-600">{{ form.errors.branch_code }}</p>
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
                        <input v-model="form.address_line_1" type="text" placeholder="123 Main Street" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900" />
                        <p v-if="form.errors.address_line_1" class="mt-1 text-xs text-red-600">{{ form.errors.address_line_1 }}</p>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Address Line 2</label>
                        <input v-model="form.address_line_2" type="text" placeholder="Building / Unit / Floor" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900" />
                        <p v-if="form.errors.address_line_2" class="mt-1 text-xs text-red-600">{{ form.errors.address_line_2 }}</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Barangay</label>
                        <input v-model="form.barangay" type="text" placeholder="Barangay" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900" />
                        <p v-if="form.errors.barangay" class="mt-1 text-xs text-red-600">{{ form.errors.barangay }}</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">City</label>
                        <input v-model="form.city" type="text" placeholder="Quezon City" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900" />
                        <p v-if="form.errors.city" class="mt-1 text-xs text-red-600">{{ form.errors.city }}</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Province</label>
                        <input v-model="form.province" type="text" placeholder="Metro Manila" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900" />
                        <p v-if="form.errors.province" class="mt-1 text-xs text-red-600">{{ form.errors.province }}</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Postal Code</label>
                        <input v-model="form.postal_code" type="text" placeholder="1100" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900" />
                        <p v-if="form.errors.postal_code" class="mt-1 text-xs text-red-600">{{ form.errors.postal_code }}</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Country Code</label>
                        <input v-model="form.country_code" type="text" maxlength="2" placeholder="PH" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm uppercase outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900" />
                        <p v-if="form.errors.country_code" class="mt-1 text-xs text-red-600">{{ form.errors.country_code }}</p>
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
                        <input v-model="form.company_email" type="email" placeholder="company@example.com" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900" />
                        <p v-if="form.errors.company_email" class="mt-1 text-xs text-red-600">{{ form.errors.company_email }}</p>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Phone</label>
                        <input v-model="form.phone" type="text" placeholder="+63 912 345 6789" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900" />
                        <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end border-t border-gray-200 bg-gray-50 px-6 py-4">
                <button type="submit" :disabled="form.processing" class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50">
                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </form>
    </div>
</template>
