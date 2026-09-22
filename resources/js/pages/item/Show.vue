<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Item {
    id: number;
    item_code: string;
    description: string;
    unit_code: string;
    unit_price: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    item: Item;
}

const props = defineProps<Props>();
</script>

<template>
    <div class="p-6 print:hidden">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Item Details
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    View item information.
                </p>
            </div>

            <Link
                href="/item"
                class="rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50"
            >
                Back to Items
            </Link>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-4 py-3">
                <h2 class="text-sm font-semibold text-gray-900">
                    Item Information
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-4 p-4 md:grid-cols-2">
                <div>
                    <p class="text-xs font-medium text-gray-500">
                        Item Code
                    </p>

                    <p class="mt-1 text-xs font-semibold text-gray-900">
                        {{ props.item.item_code }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-500">
                        Unit
                    </p>

                    <p class="mt-1 text-xs font-semibold text-gray-900">
                        {{ props.item.unit_code }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium text-gray-500">
                        Unit Price
                    </p>

                    <p class="mt-1 text-xs font-semibold text-gray-900">
                        {{ props.item.unit_price }}
                    </p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-xs font-medium text-gray-500">
                        Description
                    </p>

                    <p class="mt-1 text-xs text-gray-900">
                        {{ props.item.description }}
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-gray-200 px-4 py-3">
                <Link
                    :href="`/item/${props.item.id}/edit`"
                    class="rounded-md bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700"
                >
                    Edit Item
                </Link>
            </div>
        </div>
    </div>
</template>
