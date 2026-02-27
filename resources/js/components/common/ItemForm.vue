<script setup lang="ts">
import { useInventoryStore } from '../../stores/inventory';
import { useStockLocationStore } from '../../stores/stock-location';
import FormInput from './FormInput.vue';

const store = useInventoryStore();
const locationStore = useStockLocationStore();

// Load stock locations for dropdown
locationStore.loadStockLocations();
</script>

<template>

    <form
        v-if="store.item"
        @submit.prevent="$emit('submit-form')"
        class="space-y-6"
    >
        <!-- Required Fields -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <FormInput
                id="name"
                label="Item Name *"
                field="item.name"
                :store="store"
                :error="store.errors.name ? store.errors.name[0] : ''"
            />

            <FormInput
                id="sku"
                label="SKU"
                field="item.sku"
                :store="store"
                :error="store.errors.sku ? store.errors.sku[0] : ''"
            />

            <div>
                <label
                    for="stock_location_id"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                >
                    Stock Location
                </label>
                <select
                    id="stock_location_id"
                    v-model="store.item.stock_location_id"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                >
                    <option value="">Select a location</option>
                    <option
                        v-for="location in locationStore.paginator.data"
                        :key="location.id"
                        :value="location.id"
                    >
                        {{ location.name }}
                    </option>
                </select>
                <p
                    v-if="store.errors.stock_location_id"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ store.errors.stock_location_id[0] }}
                </p>
            </div>

            <FormInput
                id="position"
                label="Position"
                field="item.position"
                :store="store"
                :error="
                    store.errors.position
                        ? store.errors.position[0]
                        : ''
                "
            />
        </div>

        <!-- Description -->
        <div>
            <label
                for="description"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
            >
                Description
            </label>
            <textarea
                id="description"
                v-model="store.item.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
            ></textarea>
            <p
                v-if="store.errors.description"
                class="mt-1 text-sm text-red-600"
            >
                {{ store.errors.description[0] }}
            </p>
        </div>

        <!-- Quantity & Stock Levels -->
        <div
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4"
        >
            <FormInput
                id="quantity"
                label="Quantity"
                field="item.quantity"
                :store="store"
                type="number"
                :min="0"
                :error="
                    store.errors.quantity
                        ? store.errors.quantity[0]
                        : ''
                "
            />

            <FormInput
                id="reorder_point"
                label="Reorder Point"
                field="item.reorder_point"
                :store="store"
                type="number"
                :min="0"
                :error="
                    store.errors.reorder_point
                        ? store.errors.reorder_point[0]
                        : ''
                "
            />

            <FormInput
                id="reorder_quantity"
                label="Reorder Quantity"
                field="item.reorder_quantity"
                :store="store"
                type="number"
                :min="0"
                :error="
                    store.errors.reorder_quantity
                        ? store.errors.reorder_quantity[0]
                        : ''
                "
            />

            <FormInput
                id="unit_price"
                label="Unit Price"
                field="item.unit_price"
                :store="store"
                type="number"
                :min="0"
                :step="0.01"
                :error="
                    store.errors.unit_price
                        ? store.errors.unit_price[0]
                        : ''
                "
            />
        </div>

        <!-- Stock Level Constraints -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <FormInput
                id="min_stock_level"
                label="Minimum Stock Level"
                field="item.min_stock_level"
                :store="store"
                type="number"
                :min="0"
                :error="
                    store.errors.min_stock_level
                        ? store.errors.min_stock_level[0]
                        : ''
                "
            />

            <FormInput
                id="max_stock_level"
                label="Maximum Stock Level"
                field="item.max_stock_level"
                :store="store"
                type="number"
                :min="0"
                :error="
                    store.errors.max_stock_level
                        ? store.errors.max_stock_level[0]
                        : ''
                "
            />
        </div>

        <!-- Unit & Expiration Date -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <FormInput
                id="unit"
                label="Unit"
                field="item.unit"
                :store="store"
                :error="store.errors.unit ? store.errors.unit[0] : ''"
            />

            <FormInput
                id="expiration_date"
                label="Expiration Date"
                field="expiration_date"
                :store="store"
                type="date"
                :error="
                    store.errors.expiration_date
                        ? store.errors.expiration_date[0]
                        : ''
                "
            />
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button
                type="submit"
                :disabled="store.creating || store.updating"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ store.creating || store.updating ? "Saving..." : "Save" }}
            </button>
        </div>
    </form>

</template>
