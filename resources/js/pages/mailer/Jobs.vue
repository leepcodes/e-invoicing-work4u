<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Eye, Mail } from '@lucide/vue';

interface Buyer {
    id: number;
    registered_name: string;
    email: string | null;
}

interface EmailJob {
    id: number;
    status: string;
    total_invoices: number;
    successful_count: number;
    failed_count: number;
    created_at: string;
    buyer: Buyer;
}

interface Pagination {
    data: EmailJob[];
    current_page: number;
    last_page: number;
    total: number;
}

const props = defineProps<{
    emailJobs: Pagination;
}>();

const viewJob = (id: number) => {
    router.visit(`/mailer/${id}`);
};
</script>

<template>
    <Head title="Email Jobs" />

    <div class="space-y-6 p-6">
        <div>
            <h1 class="flex items-center gap-2 text-xl font-semibold">
                <Mail class="h-5 w-5" />
                Email Jobs
            </h1>

            <p class="text-sm text-gray-500">
                View your invoice email sending history.
            </p>
        </div>

        <div class="overflow-hidden rounded-lg border bg-white">
            <table class="w-full text-sm">
                <thead class="border-b bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Buyer</th>
                        <th class="px-4 py-3 text-left font-medium">Invoices</th>
                        <th class="px-4 py-3 text-left font-medium">Status</th>
                        <th class="px-4 py-3 text-left font-medium">Date</th>
                        <th class="px-4 py-3 text-right font-medium">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="job in emailJobs.data"
                        :key="job.id"
                        class="border-b last:border-0 hover:bg-gray-50"
                    >
                        <td class="px-4 py-4">
                            <p class="font-medium">
                                {{ job.buyer?.registered_name }}
                            </p>

                            <p class="text-xs text-gray-500">
                                {{ job.buyer?.email ?? 'No email' }}
                            </p>
                        </td>

                        <td class="px-4 py-4">
                            {{ job.total_invoices }}
                        </td>

                        <td class="px-4 py-4">
                            <span class="rounded-full bg-gray-100 px-2 py-1 text-xs">
                                {{ job.status }}
                            </span>
                        </td>

                        <td class="px-4 py-4 text-gray-500">
                            {{ job.created_at }}
                        </td>

                        <td class="px-4 py-4 text-right">
                            <button
                                type="button"
                                @click="viewJob(job.id)"
                                class="inline-flex items-center gap-2 rounded-md border px-3 py-2 text-xs font-medium hover:bg-gray-50"
                            >
                                <Eye class="h-4 w-4" />
                                View
                            </button>
                        </td>
                    </tr>

                    <tr v-if="!emailJobs.data.length">
                        <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                            No email jobs found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
