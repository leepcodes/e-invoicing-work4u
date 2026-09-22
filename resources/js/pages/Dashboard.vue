<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Users, FileText, CircleCheck, Clock, CircleDollarSign } from '@lucide/vue';

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: '/dashboard' }],
    },
});

interface User {
    id: number;
    name: string;
    email: string;
}

interface Dashboard {
    buyerCount: number;
    invoiceCount: number;
    paidInvoiceCount: number;
    unpaidInvoiceCount: number;
    partiallyPaidInvoiceCount: number;
}

const props = defineProps<{ user: User; dashboard: Dashboard }>();
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-5 overflow-x-auto rounded-xl bg-white p-5">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
                Welcome back, <span class="text-blue-600">{{ props.user.name }}</span>
            </h1>
            <p class="mt-1 text-sm text-gray-500">Here's an overview of your business activity.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Buyers</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ props.dashboard.buyerCount }}</p>
                        <p class="mt-1 text-xs text-gray-400">Registered buyers</p>
                    </div>
                    <div class="rounded-lg bg-blue-50 p-3"><Users class="h-6 w-6 text-blue-600" /></div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Invoices</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ props.dashboard.invoiceCount }}</p>
                        <p class="mt-1 text-xs text-gray-400">All created invoices</p>
                    </div>
                    <div class="rounded-lg bg-purple-50 p-3"><FileText class="h-6 w-6 text-purple-600" /></div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Paid Invoices</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ props.dashboard.paidInvoiceCount }}</p>
                        <p class="mt-1 text-xs text-green-600">Successfully paid</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-3"><CircleCheck class="h-6 w-6 text-green-600" /></div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Unpaid Invoices</p>
                        <p class="mt-2 text-3xl font-semibold text-gray-900">{{ props.dashboard.unpaidInvoiceCount }}</p>
                        <p class="mt-1 text-xs text-red-600">Payment required</p>
                    </div>
                    <div class="rounded-lg bg-red-50 p-3"><Clock class="h-6 w-6 text-red-600" /></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm lg:col-span-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Invoice Overview</h2>
                        <p class="mt-1 text-sm text-gray-500">Current payment status of your invoices.</p>
                    </div>
                    <FileText class="h-5 w-5 text-gray-400" />
                </div>

                <div class="mt-6 space-y-5">
                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>
                                <span class="text-sm font-medium text-gray-700">Paid</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ props.dashboard.paidInvoiceCount }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-green-500" :style="{ width: props.dashboard.invoiceCount ? `${(props.dashboard.paidInvoiceCount / props.dashboard.invoiceCount) * 100}%` : '0%' }"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
                                <span class="text-sm font-medium text-gray-700">Unpaid</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ props.dashboard.unpaidInvoiceCount }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-red-500" :style="{ width: props.dashboard.invoiceCount ? `${(props.dashboard.unpaidInvoiceCount / props.dashboard.invoiceCount) * 100}%` : '0%' }"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-yellow-500"></span>
                                <span class="text-sm font-medium text-gray-700">Partially Paid</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ props.dashboard.partiallyPaidInvoiceCount }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-yellow-500" :style="{ width: props.dashboard.invoiceCount ? `${(props.dashboard.partiallyPaidInvoiceCount / props.dashboard.invoiceCount) * 100}%` : '0%' }"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Payment Summary</h2>
                    <p class="mt-1 text-sm text-gray-500">Invoice status at a glance.</p>
                </div>

                <div class="mt-5 space-y-3">
                    <div class="flex items-center justify-between rounded-lg bg-green-50 px-4 py-3">
                        <div class="flex items-center gap-3">
                            <CircleCheck class="h-5 w-5 text-green-600" />
                            <span class="text-sm font-medium text-gray-700">Paid</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ props.dashboard.paidInvoiceCount }}</span>
                    </div>

                    <div class="flex items-center justify-between rounded-lg bg-yellow-50 px-4 py-3">
                        <div class="flex items-center gap-3">
                            <CircleDollarSign class="h-5 w-5 text-yellow-600" />
                            <span class="text-sm font-medium text-gray-700">Partially Paid</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ props.dashboard.partiallyPaidInvoiceCount }}</span>
                    </div>

                    <div class="flex items-center justify-between rounded-lg bg-red-50 px-4 py-3">
                        <div class="flex items-center gap-3">
                            <Clock class="h-5 w-5 text-red-600" />
                            <span class="text-sm font-medium text-gray-700">Unpaid</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ props.dashboard.unpaidInvoiceCount }}</span>
                    </div>
                </div>

                <div class="mt-5 border-t border-gray-100 pt-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Total invoices</span>
                        <span class="text-lg font-semibold text-gray-900">{{ props.dashboard.invoiceCount }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
