<script setup lang="ts">
import { ref, watch } from "vue";
import Content from "../../common/Content.vue";
import Card from "../../common/Card.vue";
import LoadItems from "../../../actions/App/Actions/InventoryItem/LoadItems";
import axios from "axios";
import { Pagination } from "../../../types/common";
import { InventoryItem } from "../../../types/inventory";

const paginator = ref({} as Pagination);
const sortField = ref<string | null>(null);
const sortDirection = ref<"asc" | "desc">("asc");

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

// Load items when sort changes
watch([sortField, sortDirection], () => {
    loadItems(1);
});

loadItems();
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
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination Controls -->
            <div class="mt-4">
                <nav
                    class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 sm:px-6"
                    aria-label="Pagination"
                >
                    <div class="hidden sm:block">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Showing
                            <span class="font-medium">{{
                                (paginator.current_page - 1) *
                                    paginator.per_page +
                                1
                            }}</span>
                            to
                            <span class="font-medium">{{
                                Math.min(
                                    paginator.current_page * paginator.per_page,
                                    paginator.total,
                                )
                            }}</span>
                            of
                            <span class="font-medium">{{
                                paginator.total
                            }}</span>
                            results
                        </p>
                    </div>
                    <div class="flex-1 flex justify-between sm:justify-end">
                        <a
                            v-for="link in paginator.links"
                            :key="link.label"
                            :href="link.url"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
                            :class="{
                                'bg-gray-100 dark:bg-gray-700': link.active,
                            }"
                            @click.prevent="
                                link.url &&
                                loadItems(
                                    parseInt(link.page as unknown as string),
                                )
                            "
                            v-html="link.label"
                        >
                        </a>
                    </div>
                </nav>
            </div>
        </Card>
    </Content>
</template>
