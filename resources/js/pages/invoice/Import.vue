<script setup lang="ts">
import { ref, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import * as XLSX from 'xlsx';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Invoice', href: '/invoice' },
            { title: 'Import CSV', href: '/invoice/import' },
        ],
    },
});

interface ImportJob {
    id: number;
    file_name: string;
    status: string;
    total_rows: number;
    processed_rows: number;
    successful_rows: number;
    failed_rows: number;
    skipped_rows?: number;
    error_message: string | null;
    started_at: string | null;
    completed_at: string | null;
}

interface InertiaPage {
    props: { importJob?: ImportJob };
}

const props = withDefaults(
    defineProps<{
        importJob?: ImportJob | null;
        recentJobs?: ImportJob[];
    }>(),
    { importJob: null, recentJobs: () => [] },
);

const recentJobs = ref<ImportJob[]>(props.recentJobs ?? []);

const file = ref<File | null>(null);
const uploading = ref(false);
const error = ref('');
const importJob = ref<ImportJob | null>(props.importJob ?? null);
const fileInput = ref<HTMLInputElement | null>(null);

let pollTimer: ReturnType<typeof setInterval> | null = null;

const selectFile = (event: Event) => {
    const target = event.target as HTMLInputElement;
    error.value = '';

    if (!target.files || target.files.length === 0) {
        file.value = null;
        return;
    }

    const selectedFile = target.files[0];
    const fileName = selectedFile.name.toLowerCase();

    if (!fileName.endsWith('.csv') && !fileName.endsWith('.xlsx')) {
        error.value = 'Please select a CSV or Excel (.xlsx) file.';
        file.value = null;
        return;
    }

    if (selectedFile.size > 50 * 1024 * 1024) {
        error.value = 'The file must not exceed 50 MB.';
        file.value = null;
        return;
    }

    file.value = selectedFile;
};

const prepareFile = async (selectedFile: File): Promise<File> => {
    if (selectedFile.name.toLowerCase().endsWith('.csv')) {
        return selectedFile;
    }

    const buffer = await selectedFile.arrayBuffer();

    const workbook = XLSX.read(buffer, {
        type: 'array',
    });

    const firstSheetName = workbook.SheetNames[0];

    if (!firstSheetName) {
        throw new Error('The Excel file does not contain a worksheet.');
    }

    const worksheet = workbook.Sheets[firstSheetName];

    const rows = XLSX.utils.sheet_to_json(worksheet, {
        header: 1,
        defval: '',
        raw: false,
    }) as string[][];

    const headerIndex = rows.findIndex((row) =>
        row.some(
            (cell) => String(cell).trim().toLowerCase() === 'tin'
        )
    );

    if (headerIndex === -1) {
        throw new Error(
            'The Excel file does not contain the required "tin" column.'
        );
    }

    const dataRows = rows.slice(headerIndex);

    const csv = XLSX.utils.aoa_to_sheet(dataRows);

    const csvContent = XLSX.utils.sheet_to_csv(csv);

    if (!csvContent.trim()) {
        throw new Error('The Excel file is empty.');
    }

    return new File(
        [csvContent],
        selectedFile.name.replace(/\.xlsx$/i, '.csv'),
        { type: 'text/csv' }
    );
};

const removeFile = () => {
    file.value = null;
    if (fileInput.value) fileInput.value.value = '';
    error.value = '';
};

const upload = async () => {
    if (!file.value) {
        error.value = 'Please select a CSV or Excel file first.';
        return;
    }

    uploading.value = true;
    error.value = '';

    try {
        const convertedFile = await prepareFile(file.value);

        const formData = new FormData();
        formData.append('file', convertedFile);

        router.post('/invoice/import', formData, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: (page) => {
                uploading.value = false;
                file.value = null;

                if (fileInput.value) {
                    fileInput.value.value = '';
                }

                const inertiaPage = page as unknown as InertiaPage;

                if (inertiaPage.props?.importJob) {
                    importJob.value = inertiaPage.props.importJob;
                    prependToRecent(importJob.value);
                    startPolling();
                }
            },
            onError: (errors) => {
                uploading.value = false;

                const firstError = Object.values(errors)[0];

                error.value =
                    typeof firstError === 'string'
                        ? firstError
                        : 'Unable to upload the file.';
            },
            onFinish: () => {
                uploading.value = false;
            },
        });
    } catch (e) {
        uploading.value = false;

        error.value =
            e instanceof Error
                ? e.message
                : 'Unable to process the Excel file.';
    }
};

const prependToRecent = (job: ImportJob) => {
    const existingIndex = recentJobs.value.findIndex((j) => j.id === job.id);
    if (existingIndex !== -1) {
        recentJobs.value.splice(existingIndex, 1);
    }
    recentJobs.value = [job, ...recentJobs.value].slice(0, 8);
};

const startPolling = () => {
    stopPolling();
    pollStatus();
    pollTimer = setInterval(pollStatus, 2000);
};

const pollStatus = async () => {
    const currentJob = importJob.value;
    if (!currentJob) return;

    try {
        const response = await fetch(`/invoice/import/${currentJob.id}`, {
            method: 'GET',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });

        if (!response.ok) return;

        const job = (await response.json()) as ImportJob;
        importJob.value = job;
        prependToRecent(job);

        if (job.status === 'COMPLETED' || job.status === 'FAILED') stopPolling();
    } catch (e) {
        console.error('Import polling error:', e);
    }
};

const stopPolling = () => {
    if (pollTimer !== null) {
        clearInterval(pollTimer);
        pollTimer = null;
    }
};

const goBack = () => {
    stopPolling();
    router.visit('/invoice');
};

const formatFileSize = (bytes: number) => {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / 1024 / 1024).toFixed(1)} MB`;
};

const progress = () => {
    const job = importJob.value;
    if (!job) return 0;

    if (job.total_rows > 0) return Math.min(100, Math.round((job.processed_rows / job.total_rows) * 100));
    if (job.status === 'PROCESSING') return 50;
    if (job.status === 'COMPLETED') return 100;

    return 0;
};

const statusClass = (status: string) => {
    switch (status) {
        case 'COMPLETED': return 'bg-green-100 text-green-700';
        case 'PROCESSING': return 'bg-blue-100 text-blue-700';
        case 'FAILED': return 'bg-red-100 text-red-700';
        case 'PENDING': return 'bg-yellow-100 text-yellow-700';
        default: return 'bg-gray-100 text-gray-700';
    }
};

if (importJob.value && ['PENDING', 'PROCESSING'].includes(importJob.value.status)) {
    startPolling();
}

onUnmounted(() => stopPolling());
</script>

<template>
    <div class="p-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Import Invoices
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Upload a CSV file to import multiple invoices.
                </p>
            </div>

        <div class="flex items-center gap-2">
            <a
                href="/invoice/import/template/csv"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50"
            >
                Download CSV Template
            </a>

            <a
                href="/invoice/import/template/xlsx"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50"
            >
                Download Excel Template
            </a>

            <button
                type="button"
                class="rounded-lg border border-gray-300 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50"
                @click="goBack"
            >
                Back to Invoice
            </button>
        </div>
    </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- LEFT: Upload + status -->
            <div class="space-y-5">
                <div class="rounded-lg border border-gray-200 bg-white p-6">
                    <h2 class="text-sm font-semibold text-gray-900">Import File</h2>
                    <p class="mt-1 text-xs text-gray-500">
                        Select a CSV or Excel (.xlsx) file containing your invoice data. Maximum file size is 50 MB.
                    </p>

                    <div class="mt-5">
                        <input
                            ref="fileInput"
                            type="file"
                            accept=".csv,.xlsx,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                            class="block w-full cursor-pointer rounded-md border border-gray-300 bg-white text-xs text-gray-600 file:mr-4 file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-xs file:font-medium file:text-gray-700 hover:file:bg-gray-200"
                            @change="selectFile"
                        />
                    </div>

                    <div v-if="file" class="mt-4 rounded-md border border-gray-200 bg-gray-50 p-3">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0">
                                <p class="truncate text-xs font-medium text-gray-900" :title="file.name">{{ file.name }}</p>
                                <p class="mt-1 text-[11px] text-gray-500">{{ formatFileSize(file.size) }}</p>
                            </div>

                            <button type="button" class="ml-4 text-xs font-medium text-red-600 hover:text-red-700" @click="removeFile">Remove</button>
                        </div>
                    </div>

                    <div v-if="error" class="mt-4 rounded-md border border-red-200 bg-red-50 px-3 py-2">
                        <p class="text-xs text-red-600">{{ error }}</p>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button type="button" :disabled="uploading || !file" class="rounded-md bg-blue-600 px-4 py-2 text-xs font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50" @click="upload">
                            <span v-if="uploading">Uploading...</span>
                            <span v-else>Upload File</span>
                        </button>
                    </div>
                </div>

                <div v-if="importJob" class="rounded-lg border border-gray-200 bg-white p-6">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0">
                            <h2 class="text-sm font-semibold text-gray-900">Import Status</h2>
                            <p class="mt-1 truncate text-xs text-gray-500" :title="importJob.file_name">{{ importJob.file_name }}</p>
                        </div>

                        <span class="ml-4 rounded-full px-2.5 py-1 text-[10px] font-semibold" :class="statusClass(importJob.status)">
                            {{ importJob.status }}
                        </span>
                    </div>

                    <div class="mt-6">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-600">Progress</span>
                            <span class="text-xs font-semibold text-gray-900">{{ progress() }}%</span>
                        </div>

                        <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                            <div
                                class="h-full rounded-full bg-blue-600 transition-all duration-500"
                                :class="{ 'animate-pulse': importJob.status === 'PROCESSING' && importJob.total_rows === 0 }"
                                :style="{ width: `${progress()}%` }"
                            ></div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-4 gap-3">
                        <div class="rounded-md border border-gray-200 bg-gray-50 p-3">
                            <p class="text-[10px] uppercase text-gray-500">Processed</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ importJob.processed_rows }}</p>
                        </div>

                        <div class="rounded-md border border-gray-200 bg-gray-50 p-3">
                            <p class="text-[10px] uppercase text-gray-500">Successful</p>
                            <p class="mt-1 text-lg font-semibold text-green-600">{{ importJob.successful_rows }}</p>
                        </div>

                        <div class="rounded-md border border-gray-200 bg-gray-50 p-3">
                            <p class="text-[10px] uppercase text-gray-500">Skipped</p>
                            <p class="mt-1 text-lg font-semibold text-yellow-600">{{ importJob.skipped_rows ?? 0 }}</p>
                        </div>

                        <div class="rounded-md border border-gray-200 bg-gray-50 p-3">
                            <p class="text-[10px] uppercase text-gray-500">Failed</p>
                            <p class="mt-1 text-lg font-semibold text-red-600">{{ importJob.failed_rows }}</p>
                        </div>
                    </div>

                    <div v-if="importJob.total_rows > 0" class="mt-4 text-xs text-gray-500">
                        Total rows: <span class="font-medium text-gray-700">{{ importJob.total_rows }}</span>
                    </div>

                    <div v-if="importJob.status === 'FAILED' && importJob.error_message" class="mt-5 rounded-md border border-red-200 bg-red-50 p-3">
                        <p class="text-xs font-medium text-red-700">Import failed</p>
                        <p class="mt-1 whitespace-pre-line text-xs text-red-600">{{ importJob.error_message }}</p>
                    </div>

                    <div v-if="importJob.status === 'COMPLETED'" class="mt-5 rounded-md border border-green-200 bg-green-50 p-3">
                        <p class="text-xs font-medium text-green-700">Import completed successfully.</p>
                        <p class="mt-1 text-xs text-green-600">
                            {{ importJob.successful_rows }} invoice(s) imported successfully.
                            <span v-if="(importJob.skipped_rows ?? 0) > 0">
                                {{ importJob.skipped_rows }} row(s) skipped because they were duplicates.
                            </span>
                            <span v-if="importJob.failed_rows > 0">
                                {{ importJob.failed_rows }} row(s) failed.
                            </span>
                        </p>
                    </div>

                    <div v-if="importJob.status === 'PROCESSING' || importJob.status === 'PENDING'" class="mt-5 rounded-md border border-blue-200 bg-blue-50 p-3">
                        <p class="text-xs font-medium text-blue-700">Import is being processed.</p>
                        <p class="mt-1 text-xs text-blue-600">You can wait here while the invoices are imported.</p>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Recent imports mini table -->
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h2 class="text-sm font-semibold text-gray-900">Recent Imports</h2>
                <p class="mt-1 text-xs text-gray-500">Your most recent File import jobs.</p>

                <div v-if="recentJobs.length === 0" class="mt-6 flex flex-col items-center justify-center rounded-md border border-dashed border-gray-200 py-10 text-center">
                    <p class="text-xs text-gray-400">No import history yet.</p>
                </div>

                <div v-else class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead>
                            <tr class="text-left text-[10px] uppercase text-gray-500">
                                <th class="py-2 pr-3 font-medium">File</th>
                                <th class="py-2 px-3 font-medium text-right">Total</th>
                                <th class="py-2 px-3 font-medium text-right">Processed</th>
                                <th class="py-2 px-3 font-medium text-right">Success</th>
                                <th class="py-2 px-3 font-medium text-right">Skipped</th>
                                <th class="py-2 px-3 font-medium text-right">Failed</th>
                                <th class="py-2 pl-3 font-medium text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="job in recentJobs" :key="job.id" class="hover:bg-gray-50">
                                <td class="max-w-[140px] truncate py-2.5 pr-3 font-medium text-gray-900" :title="job.file_name">
                                    {{ job.file_name }}
                                </td>
                                <td class="py-2.5 px-3 text-right text-gray-700">{{ job.total_rows }}</td>
                                <td class="py-2.5 px-3 text-right text-gray-700">{{ job.processed_rows }}</td>
                                <td class="py-2.5 px-3 text-right text-green-600">{{ job.successful_rows }}</td>
                                <td class="py-2.5 px-3 text-right text-yellow-600">{{ job.skipped_rows }}</td>
                                <td class="py-2.5 px-3 text-right text-red-600">{{ job.failed_rows }}</td>
                                <td class="py-2.5 pl-3 text-right">
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold" :class="statusClass(job.status)">
                                        {{ job.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
