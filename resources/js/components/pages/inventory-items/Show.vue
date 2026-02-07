<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import Content from "../../common/Content.vue";
import Card from "../../common/Card.vue";
import axios from "axios";
import type { InventoryItem } from "../../../types/inventory";

const router = useRouter();
const route = useRoute();

const item = ref<InventoryItem | null>(null);
const loading = ref(true);
const deleting = ref(false);

const loadItem = async () => {
    try {
        const itemId = route.params.id as string;
        const response = await axios.get(`/api/inventory-items/${itemId}`);
        item.value = response.data;
    } catch (error) {
        console.error("Error loading inventory item:", error);
    } finally {
        loading.value = false;
    }
};

const deleteItem = async () => {
    if (!item.value) {
        return;
    }

    if (!confirm("Are you sure you want to delete this inventory item?")) {
        return;
    }

    try {
        deleting.value = true;
        await axios.delete(`/api/inventory-items/${item.value.id}`);
        router.push("/inventory");
    } catch (error) {
        console.error("Error deleting inventory item:", error);
    } finally {
        deleting.value = false;
    }
};

onMounted(() => {
    loadItem();
});
</script>

<template>
    <Content>
        <Card v-if="loading">
            <template #title>Loading...</template>
            <p>Loading inventory item details...</p>
        </Card>

        <Card v-else-if="item">
            <template #title>{{ item.name }}</template>

            <div class="space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            SKU
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ item.sku || "N/A" }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Stock Location
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ item.stock_location?.name || "N/A" }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Position
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ item.position || "N/A" }}
                        </p>
                    </div>
                </div>

                <!-- Description -->
                <div v-if="item.description">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Description
                    </label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ item.description }}
                    </p>
                </div>

                <!-- Quantity & Stock Levels -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Current Quantity
                        </label>
                        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ item.quantity }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Reorder Point
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ item.reorder_point }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Reorder Quantity
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ item.reorder_quantity }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Unit Price
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            ${{ Number(item.unit_price).toFixed(2) }}
                        </p>
                    </div>
                </div>

                <!-- Stock Level Constraints -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Minimum Stock Level
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ item.min_stock_level }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Maximum Stock Level
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ item.max_stock_level }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Unit
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ item.unit || "N/A" }}
                        </p>
                    </div>
                </div>

                <!-- Expiration Date & Timestamps -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div v-if="item.expiration_date">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Expiration Date
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ item.expiration_date }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Created At
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ item.created_at }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Updated At
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ item.updated_at }}
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button
                        type="button"
                        @click="router.back()"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Back
                    </button>
                    <button
                        type="button"
                        @click="deleteItem"
                        :disabled="deleting"
                        class="px-4 py-2 bg-red-600 text-white rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ deleting ? "Deleting..." : "Delete" }}
                    </button>
                </div>
            </div>
        </Card>

        <Card v-else>
            <template #title>Not Found</template>
            <p class="text-gray-900 dark:text-gray-100">Inventory item not found.</p>
            <button
                type="button"
                @click="router.push('/inventory')"
                class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700"
            >
                Back to Inventory
            </button>
        </Card>
    </Content>
</template>
