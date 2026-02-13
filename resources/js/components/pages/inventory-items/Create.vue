<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import Content from "../../common/Content.vue";
import Card from "../../common/Card.vue";
import { useInventoryStore } from "../../../stores/inventory";

const store = useInventoryStore();

const router = useRouter();

// Initialize form
store.initItem();

// Temp Stock locations for dropdown
const stockLocations = ref([
    { id: 1, name: "Basement" },
    { id: 2, name: "Garage" },
    { id: 3, name: "Living Room" },
    { id: 4, name: "Kitchen" },
]);

// Handle form submission
const submitForm = async () => {
    if (!(await store.createItem())) return;
    // Redirect to inventory items list on success
    router.push("/inventory");
};
</script>

<template>
    <Content>
        <Card>
            <template #title>Create New Inventory Item</template>

            <form
                v-if="store.item"
                @submit.prevent="submitForm"
                class="space-y-6"
            >
                <!-- Required Fields -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label
                            for="name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Item Name *
                        </label>
                        <input
                            id="name"
                            v-model="store.item.name"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                            required
                        />
                        <p
                            v-if="store.errors.name"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ store.errors.name[0] }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="sku"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            SKU
                        </label>
                        <input
                            id="sku"
                            v-model="store.item.sku"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="store.errors.sku"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ store.errors.sku[0] }}
                        </p>
                    </div>

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
                                v-for="location in stockLocations"
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

                    <div>
                        <label
                            for="position"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Position
                        </label>
                        <input
                            id="position"
                            v-model="store.item.position"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="store.errors.position"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ store.errors.position[0] }}
                        </p>
                    </div>
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
                    <div>
                        <label
                            for="quantity"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Quantity
                        </label>
                        <input
                            id="quantity"
                            v-model="store.item.quantity"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="store.errors.quantity"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ store.errors.quantity[0] }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="reorder_point"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Reorder Point
                        </label>
                        <input
                            id="reorder_point"
                            v-model="store.item.reorder_point"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="store.errors.reorder_point"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ store.errors.reorder_point[0] }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="reorder_quantity"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Reorder Quantity
                        </label>
                        <input
                            id="reorder_quantity"
                            v-model="store.item.reorder_quantity"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="store.errors.reorder_quantity"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ store.errors.reorder_quantity[0] }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="unit_price"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Unit Price ($)
                        </label>
                        <input
                            id="unit_price"
                            v-model="store.item.unit_price"
                            type="number"
                            min="0"
                            step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="store.errors.unit_price"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ store.errors.unit_price[0] }}
                        </p>
                    </div>
                </div>

                <!-- Stock Level Constraints -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label
                            for="min_stock_level"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Minimum Stock Level
                        </label>
                        <input
                            id="min_stock_level"
                            v-model="store.item.min_stock_level"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="store.errors.min_stock_level"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ store.errors.min_stock_level[0] }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="max_stock_level"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Maximum Stock Level
                        </label>
                        <input
                            id="max_stock_level"
                            v-model="store.item.max_stock_level"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="store.errors.max_stock_level"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ store.errors.max_stock_level[0] }}
                        </p>
                    </div>
                </div>

                <!-- Unit & Expiration Date -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label
                            for="unit"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Unit
                        </label>
                        <input
                            id="unit"
                            v-model="store.item.unit"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="store.errors.unit"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ store.errors.unit[0] }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="expiration_date"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                        >
                            Expiration Date
                        </label>
                        <input
                            id="expiration_date"
                            v-model="store.item.expiration_date"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="store.errors.expiration_date"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ store.errors.expiration_date[0] }}
                        </p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Create Inventory Item
                    </button>
                </div>
            </form>
        </Card>
    </Content>
</template>
