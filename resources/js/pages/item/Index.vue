<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Item {
    id: number;
    item_code: string;
    description: string;
    unit_code: string;
    unit_price: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedItems {
    data: Item[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
}

interface Props {
    items: PaginatedItems;
    filters: {
        search?: string;
    };
}

const props = defineProps<Props>();

const search = ref(props.filters?.search ?? '');

const searchItems = () => {
    router.get(
        '/item',
        {
            search: search.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const clearSearch = () => {
    search.value = '';

    router.get(
        '/item',
        {},
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const goToPage = (url: string | null) => {
    if (!url) return;

    router.visit(url, {
        preserveState: true,
        preserveScroll: true,
    });
};

const deleteItem = (item: Item) => {
    if (!confirm(`Delete ${item.item_code}?`)) return;

    router.delete(`/item/${item.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="p-6 print:hidden">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Items
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage your item master list.
                </p>
            </div>

            <Link
                href="/item/create"
                class="rounded-lg bg-blue-600 px-5 py-3 text-xs font-semibold uppercase tracking-wider text-white hover:bg-blue-700"
            >
                Create Item
            </Link>
        </div>

        <div class="mb-4 rounded-lg border border-gray-200 bg-white p-3">
            <form
                @submit.prevent="searchItems"
                class="flex items-end gap-2"
            >
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-medium text-gray-600">
                        Search
                    </label>

                    <input
                        v-model="search"
                        type="text"
                        placeholder="Item code or description..."
                        class="w-full rounded-md border border-gray-300 px-3 py-2 text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    />
                </div>

                <button
                    type="submit"
                    class="rounded-md bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700"
                >
                    Filter
                </button>

                <button
                    type="button"
                    class="rounded-md border border-gray-300 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50"
                    @click="clearSearch"
                >
                    Clear
                </button>
            </form>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
            <table class="w-full table-fixed divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            class="w-[20%] px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500"
                        >
                            Item Code
                        </th>

                        <th
                            class="w-[30%] px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500"
                        >
                            Description
                        </th>

                        <th
                            class="w-[10%] px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500"
                        >
                            Unit
                        </th>

                        <th
                            class="w-[20%] px-3 py-2.5 text-left text-[10px] font-semibold uppercase tracking-wider text-gray-500"
                        >
                            Unit Price
                        </th>

                        <th
                            class="w-[20%] px-3 py-2.5 text-center text-[10px] font-semibold uppercase tracking-wider text-gray-500"
                        >
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    <tr v-if="props.items.data.length === 0">
                        <td
                            colspan="4"
                            class="px-4 py-10 text-center"
                        >
                            <p class="text-sm font-medium text-gray-700">
                                No items found
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                Try changing your search.
                            </p>
                        </td>
                    </tr>

                    <tr
                        v-for="item in props.items.data"
                        :key="item.id"
                        class="transition hover:bg-gray-50"
                    >
                        <td class="px-3 py-3">
                            <span
                                class="block truncate text-xs font-medium text-gray-900"
                                :title="item.item_code"
                            >
                                {{ item.item_code }}
                            </span>
                        </td>

                        <td class="px-3 py-3">
                            <span
                                class="block truncate text-xs text-gray-600"
                                :title="item.description"
                            >
                                {{ item.description }}
                            </span>
                        </td>

                        <td class="px-3 py-3 text-xs text-gray-600">
                            {{ item.unit_code }}
                        </td>

                        <td class="px-3 py-3 text-xs text-gray-600">
                            {{ item.unit_price }}
                        </td>

                        <td class="px-2 py-3">
                            <div class="flex justify-center gap-1">
                                <Link
                                    :href="`/item/${item.id}`"
                                    class="rounded border border-gray-300 px-2 py-1 text-[10px] font-medium text-gray-700 hover:bg-gray-100"
                                >
                                    View
                                </Link>

                                <Link
                                    :href="`/item/${item.id}/edit`"
                                    class="rounded border border-gray-300 px-2 py-1 text-[10px] font-medium text-gray-700 hover:bg-gray-100"
                                >
                                    Edit
                                </Link>

                                <button
                                    type="button"
                                    class="rounded border border-gray-300 px-2 py-1 text-[10px] font-medium text-red-600 hover:bg-red-50"
                                    @click="deleteItem(item)"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div
                v-if="props.items.last_page > 1"
                class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3"
            >
                <div class="text-xs text-gray-500">
                    Showing
                    <span class="font-medium text-gray-700">
                        {{ props.items.from }}
                    </span>
                    -
                    <span class="font-medium text-gray-700">
                        {{ props.items.to }}
                    </span>
                    of
                    <span class="font-medium text-gray-700">
                        {{ props.items.total }}
                    </span>
                </div>

                <div class="flex items-center gap-1">
                    <button
                        v-for="link in props.items.links"
                        :key="link.label"
                        type="button"
                        :disabled="!link.url"
                        class="min-w-[30px] rounded-md border px-2 py-1.5 text-[11px] font-medium"
                        :class="
                            link.active
                                ? 'border-blue-600 bg-blue-600 text-white'
                                : link.url
                                  ? 'border-gray-300 text-gray-600 hover:bg-gray-50'
                                  : 'cursor-not-allowed border-gray-200 text-gray-300'
                        "
                        v-html="link.label"
                        @click="link.url && goToPage(link.url)"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
