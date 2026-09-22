<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';

interface Item {
    id: number;
    item_code: string;
    description: string;
    unit_code: string;
    unit_price: string | number;
}

interface Props {
    item: Item;
}

const props = defineProps<Props>();

const form = useForm({
    item_code: props.item.item_code,
    description: props.item.description,
    unit_code: props.item.unit_code,
    unit_price: props.item.unit_price,
});

const submit = () => {
    form.put(`/item/${props.item.id}`);
};
</script>

<template>
    <div class="p-6 print:hidden">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">
                Edit Item
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Update the item information.
            </p>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
            <div class="border-b border-gray-200 px-4 py-3">
                <h2 class="text-sm font-semibold text-gray-900">
                    Item Information
                </h2>
            </div>

            <form
                @submit.prevent="submit"
                class="space-y-5 p-4"
            >
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">
                            Item Code
                        </label>

                        <input
                            v-model="form.item_code"
                            type="text"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :class="{ 'border-red-500': form.errors.item_code }"
                        />

                        <p
                            v-if="form.errors.item_code"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.item_code }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">
                            Unit
                        </label>

                        <select
                            v-model="form.unit_code"
                            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :class="{ 'border-red-500': form.errors.unit_code }"
                        >
                            <option value="">Select Unit</option>
                            <option value="PCS">PCS - Pieces</option>
                            <option value="BOX">BOX - Box</option>
                            <option value="SET">SET - Set</option>
                            <option value="KG">KG - Kilogram</option>
                            <option value="G">G - Gram</option>
                            <option value="L">L - Liter</option>
                            <option value="ML">ML - Milliliter</option>
                            <option value="M">M - Meter</option>
                            <option value="HR">HR - Hour</option>
                            <option value="DAY">DAY - Day</option>
                        </select>

                        <p
                            v-if="form.errors.unit_code"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.unit_code }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">
                            Unit Price
                        </label>

                        <input
                            v-model="form.unit_price"
                            type="number"
                            min="0"
                            step="0.01"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :class="{ 'border-red-500': form.errors.unit_price }"
                        />

                        <p
                            v-if="form.errors.unit_price"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.unit_price }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-gray-600">
                            Description
                        </label>

                        <input
                            v-model="form.description"
                            type="text"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            :class="{ 'border-red-500': form.errors.description }"
                        />

                        <p
                            v-if="form.errors.description"
                            class="mt-1 text-xs text-red-600"
                        >
                            {{ form.errors.description }}
                        </p>
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-200 pt-4">
                    <Link
                        href="/item"
                        class="rounded-md border border-gray-300 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50"
                    >
                        Cancel
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-md bg-blue-600 px-3 py-2 text-xs font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{ form.processing ? 'Updating...' : 'Update Item' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
