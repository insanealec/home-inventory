<script setup lang="ts">
import { ref, watch } from "vue";
import { useRouter, useRoute } from "vue-router";
import Content from "../../common/Content.vue";
import Card from "../../common/Card.vue";
import LoadItems from "../../../actions/App/Actions/InventoryItem/LoadItems";
import axios from "axios";
import { Pagination } from "../../../types/common";
import { InventoryItem } from "../../../types/inventory";
import Paginator from "../../common/Paginator.vue";

const paginator = ref<Pagination<InventoryItem>>({
    data: <InventoryItem[]>[],
} as Pagination<InventoryItem>);
const sortField = ref<string | null>(null);
const sortDirection = ref<"asc" | "desc">("asc");
const router = useRouter();
const route = useRoute();

const loadItems = async (page: number = 1) => {
    try {
        const params: any = { page };
        if (sortField.value) {
            params.sort = sortField.value;
            params.direction = sortDirection.value;
        }
        const response = await axios.get(LoadItems.url(), { params });
        paginator.value = response.data;
        console.log("Inventory items loaded:", paginator.value);
    } catch (error) {
        console.error("Error loading inventory items:", error);
    }
};

const handleSort = (field: string) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortDirection.value = "asc";
    }
    loadItems(1);
};

const deleteItem = async (item: InventoryItem) => {
    if (!confirm(`Are you sure you want to delete "${item.name}"?`)) {
        return;
    }

    try {
        await axios.delete(`/api/inventory-items/${item.id}`);
        loadItems(parseInt(route.query.page as string) || 1);
    } catch (error) {
        console.error("Error deleting inventory item:", error);
    }
};

// Load items when sort changes
watch([sortField, sortDirection], () => {
    loadItems(1);
});

// Load items when route changes (for page parameter)
watch(
    () => route.query.page,
    (newPage) => {
        const page = newPage ? parseInt(newPage as string) : 1;
        loadItems(page);
    },
);

// Initial load
loadItems(parseInt(route.query.page as string) || 1);
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
                            v-for="item in paginator.data"
                            :key="item.id"
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
                                    @click="deleteItem(item)"
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
            <Paginator :paginator="paginator" />
        </Card>
    </Content>
</template>
