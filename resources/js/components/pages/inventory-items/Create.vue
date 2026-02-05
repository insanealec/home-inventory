<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import Content from "../../common/Content.vue";
import Card from "../../common/Card.vue";
import axios from "axios";
import type { InventoryItem } from "../../../types/inventory";
import CreateItem from "../../../actions/App/Actions/InventoryItem/CreateItem";

const router = useRouter();

// Form data
const formData = ref<InventoryItem>({
    id: 0,
    name: "",
    sku: "",
    stock_location_id: null,
    position: "",
    description: "",
    quantity: 0,
    reorder_point: 0,
    reorder_quantity: 0,
    min_stock_level: 0,
    max_stock_level: 0,
    unit_price: 0,
    unit: "",
    expiration_date: null,
    user_id: 0,
    created_at: "",
    updated_at: "",
});

// Validation errors
const errors = ref<Record<string, string>>({});

// Stock locations for dropdown
const stockLocations = ref([
    { id: 1, name: "Basement" },
    { id: 2, name: "Garage" },
    { id: 3, name: "Living Room" },
    { id: 4, name: "Kitchen" },
]);

// Handle form submission
const submitForm = async () => {
    try {
        // Reset errors
        errors.value = {};

        // Submit the form
        const response = await axios.post(
            CreateItem.url(),
            formData.value,
        );

        // Redirect to inventory items list on success
        router.push("/inventory");
    } catch (error: any) {
        if (error.response && error.response.data.errors) {
            // Handle validation errors
            errors.value = error.response.data.errors;
        } else {
            console.error("Error creating inventory item:", error);
        }
    }
};
</script>

<template>
    <Content>
        <Card>
            <template #title>Create New Inventory Item</template>

            <form @submit.prevent="submitForm" class="space-y-6">
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
                            v-model="formData.name"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                            required
                        />
                        <p v-if="errors.name" class="mt-1 text-sm text-red-600">
                            {{ errors.name[0] }}
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
                            v-model="formData.sku"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p v-if="errors.sku" class="mt-1 text-sm text-red-600">
                            {{ errors.sku[0] }}
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
                            v-model="formData.stock_location_id"
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
                            v-if="errors.stock_location_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.stock_location_id[0] }}
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
                            v-model="formData.position"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="errors.position"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.position[0] }}
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
                        v-model="formData.description"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                    ></textarea>
                    <p
                        v-if="errors.description"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ errors.description[0] }}
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
                            v-model="formData.quantity"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="errors.quantity"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.quantity[0] }}
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
                            v-model="formData.reorder_point"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="errors.reorder_point"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.reorder_point[0] }}
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
                            v-model="formData.reorder_quantity"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="errors.reorder_quantity"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.reorder_quantity[0] }}
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
                            v-model="formData.unit_price"
                            type="number"
                            min="0"
                            step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="errors.unit_price"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.unit_price[0] }}
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
                            v-model="formData.min_stock_level"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="errors.min_stock_level"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.min_stock_level[0] }}
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
                            v-model="formData.max_stock_level"
                            type="number"
                            min="0"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="errors.max_stock_level"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.max_stock_level[0] }}
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
                            v-model="formData.unit"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p v-if="errors.unit" class="mt-1 text-sm text-red-600">
                            {{ errors.unit[0] }}
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
                            v-model="formData.expiration_date"
                            type="date"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        />
                        <p
                            v-if="errors.expiration_date"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.expiration_date[0] }}
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
