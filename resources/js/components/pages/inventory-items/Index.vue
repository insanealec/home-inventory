<script setup lang="ts">
import { ref, watch } from "vue";
import { useRoute } from "vue-router";
import Content from "../../common/Content.vue";
import Card from "../../common/Card.vue";
import Paginator from "../../common/Paginator.vue";
import { useInventoryStore } from "../../../stores/inventory";

const store = useInventoryStore();

const sortField = ref<string | null>(null);
const sortDirection = ref<"asc" | "desc">("asc");
const route = useRoute();

const handleSort = (field: string) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortDirection.value = "asc";
    }
    store.loadItems(1, sortField.value, sortDirection.value);
};

const loadThisPage = () => {
    const page = route.query.page ? parseInt(route.query.page as string) : 1;
    store.loadItems(page, sortField.value, sortDirection.value);
};

const deleteItem = async (id: number) => {
    await store.deleteItem(id);
    loadThisPage();
};

// Load items when sort changes
watch([sortField, sortDirection], () => {
    store.loadItems(1, sortField.value, sortDirection.value);
});

// Load items when route changes (for page parameter)
watch(
    () => route.query.page,
    (newPage) => {
        const page = newPage ? parseInt(newPage as string) : 1;
        store.loadItems(page, sortField.value, sortDirection.value);
    },
);

// Initial load
loadThisPage();
</script>

<template>
    <Content>
        <Card>
            <template #title>Inventory Items</template>
            <div class="overflow-x-auto">
                <table
                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                                @click="handleSort('name')"
                            >
                                <div class="flex items-center">
                                    Name
                                    <span
                                        v-if="sortField === 'name'"
                                        class="ml-1"
                                    >
                                        {{
                                            sortDirection === "asc" ? "↑" : "↓"
                                        }}
                                    </span>
                                </div>
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                                @click="handleSort('sku')"
                            >
                                <div class="flex items-center">
                                    SKU
                                    <span
                                        v-if="sortField === 'sku'"
                                        class="ml-1"
                                    >
                                        {{
                                            sortDirection === "asc" ? "↑" : "↓"
                                        }}
                                    </span>
                                </div>
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                                @click="handleSort('quantity')"
                            >
                                <div class="flex items-center">
                                    Quantity
                                    <span
                                        v-if="sortField === 'quantity'"
                                        class="ml-1"
                                    >
                                        {{
                                            sortDirection === "asc" ? "↑" : "↓"
                                        }}
                                    </span>
                                </div>
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                                @click="handleSort('stock_location_id')"
                            >
                                <div class="flex items-center">
                                    Stock Location
                                    <span
                                        v-if="sortField === 'stock_location_id'"
                                        class="ml-1"
                                    >
                                        {{
                                            sortDirection === "asc" ? "↑" : "↓"
                                        }}
                                    </span>
                                </div>
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer"
                                @click="handleSort('created_at')"
                            >
                                <div class="flex items-center">
                                    Created At
                                    <span
                                        v-if="sortField === 'created_at'"
                                        class="ml-1"
                                    >
                                        {{
                                            sortDirection === "asc" ? "↑" : "↓"
                                        }}
                                    </span>
                                </div>
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <tr
                            v-for="item in store.paginator.data"
                            :key="item.id as number"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800"
                        >
                            <td
                                class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100"
                            >
                                {{ item.name }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100"
                            >
                                {{ item.sku }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100"
                            >
                                {{ item.quantity }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100"
                            >
                                {{ item.stock_location?.name || "N/A" }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100"
                            >
                                {{ item.created_at }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2"
                            >
                                <router-link
                                    :to="`/inventory/${item.id}`"
                                    class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300"
                                >
                                    View
                                </router-link>
                                <button
                                    @click="deleteItem(item.id as number)"
                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination Controls -->
            <Paginator :paginator="store.paginator" />
        </Card>
    </Content>
</template>
