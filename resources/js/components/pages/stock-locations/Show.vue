<script setup lang="ts">
import { onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import Content from "../../common/Content.vue";
import Card from "../../common/Card.vue";
import { useStockLocationStore } from "../../../stores/stock-location";

const store = useStockLocationStore();

const router = useRouter();
const route = useRoute();

const deleteStockLocation = async () => {
    if (!store.stockLocation) return;
    if (await store.deleteStockLocationItem(store.stockLocation.id as number)) {
        router.push("/stock-locations");
    }
};

onMounted(() => {
    store.loadStockLocation(parseInt(route.params.id as string));
});
</script>

<template>
    <Content>
        <Card v-if="store.loading">
            <template #title>Loading...</template>
            <p>Loading stock location details...</p>
        </Card>

        <Card v-else-if="store.stockLocation">
            <template #title>{{ store.stockLocation.name }}</template>

            <div class="space-y-6">
                <!-- Basic Information -->
                <div
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Name
                        </label>
                        <p
                            class="mt-1 text-sm text-gray-900 dark:text-gray-100"
                        >
                            {{ store.stockLocation.name }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Short Name
                        </label>
                        <p
                            class="mt-1 text-sm text-gray-900 dark:text-gray-100"
                        >
                            {{ store.stockLocation.short_name }}
                        </p>
                    </div>
                </div>

                <!-- Description -->
                <div v-if="store.stockLocation.description">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Description
                    </label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                        {{ store.stockLocation.description }}
                    </p>
                </div>

                <!-- Timestamps -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Created At
                        </label>
                        <p
                            class="mt-1 text-sm text-gray-900 dark:text-gray-100"
                        >
                            {{ store.stockLocation.created_at }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                        >
                            Updated At
                        </label>
                        <p
                            class="mt-1 text-sm text-gray-900 dark:text-gray-100"
                        >
                            {{ store.stockLocation.updated_at }}
                        </p>
                    </div>
                </div>

                <!-- Inventory Items Count -->
                <div v-if="store.stockLocation.inventory_items">
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Items in Location
                    </label>
                    <p
                        class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100"
                    >
                        {{ store.stockLocation.inventory_items.length }}
                    </p>
                </div>

                <!-- Action Buttons -->
                <div
                    class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700"
                >
                    <button
                        type="button"
                        @click="router.back()"
                        class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Back
                    </button>
                    <router-link
                        :to="`/stock-locations/${store.stockLocation.id}/edit`"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Edit
                    </router-link>
                    <button
                        type="button"
                        @click="deleteStockLocation"
                        :disabled="store.deleting"
                        class="px-4 py-2 bg-red-600 text-white rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ store.deleting ? "Deleting..." : "Delete" }}
                    </button>
                </div>
            </div>
        </Card>

        <Card v-else>
            <template #title>Not Found</template>
            <p class="text-gray-900 dark:text-gray-100">
                Stock location not found.
            </p>
            <button
                type="button"
                @click="router.push('/stock-locations')"
                class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700"
            >
                Back to Stock Locations
            </button>
        </Card>
    </Content>
</template>
