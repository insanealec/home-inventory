<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useTokenStore } from "@/stores/token";
import Card from "../common/Card.vue";
import Content from "../common/Content.vue";

const tokenStore = useTokenStore();
const summary = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        const response = await axios.get("/api/dashboard");
        summary.value = response.data;
    } catch (error) {
        console.error("Error loading dashboard:", error);
    } finally {
        loading.value = false;
    }
    tokenStore.loadTokens();
});
</script>

<template>
    <Content>
        <!-- Summary Grid -->
        <div v-if="summary && !loading" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <Card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                        {{ summary.total_items }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Total Items
                    </div>
                </div>
            </Card>

            <Card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                        {{ summary.total_locations }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Stock Locations
                    </div>
                </div>
            </Card>

            <Card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                        {{ summary.active_shopping_lists }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Active Lists
                    </div>
                </div>
            </Card>

            <Card>
                <div class="text-center">
                    <div
                        :class="[
                            summary.low_stock_items.length > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400',
                            'text-3xl font-bold',
                        ]"
                    >
                        {{ summary.low_stock_items.length }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Low Stock
                    </div>
                </div>
            </Card>
        </div>

        <!-- Quick Actions -->
        <Card>
            <template #title>Quick Actions</template>
            <div class="flex flex-wrap gap-3">
                <router-link
                    to="/inventory/create"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                >
                    Add Item
                </router-link>
                <router-link
                    to="/stock-locations/create"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                    Add Location
                </router-link>
                <router-link
                    to="/shopping-lists/create"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                >
                    New Shopping List
                </router-link>
            </div>
        </Card>

        <!-- Low Stock Items -->
        <Card v-if="summary && summary.low_stock_items.length > 0">
            <template #title>Low Stock Items</template>
            <div class="space-y-2">
                <div
                    v-for="item in summary.low_stock_items"
                    :key="item.id"
                    class="flex justify-between items-center p-2 bg-red-50 dark:bg-red-900/20 rounded"
                >
                    <router-link
                        :to="`/inventory/${item.id}`"
                        class="text-indigo-600 dark:text-indigo-400 hover:underline"
                    >
                        {{ item.name }}
                    </router-link>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ item.quantity }} / {{ item.reorder_point }}
                    </span>
                </div>
            </div>
        </Card>

        <!-- Expiring Soon Items -->
        <Card v-if="summary && summary.expiring_items.length > 0">
            <template #title>Expiring Soon</template>
            <div class="space-y-2">
                <div
                    v-for="item in summary.expiring_items"
                    :key="item.id"
                    class="flex justify-between items-center p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded"
                >
                    <router-link
                        :to="`/inventory/${item.id}`"
                        class="text-indigo-600 dark:text-indigo-400 hover:underline"
                    >
                        {{ item.name }}
                    </router-link>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Expires: {{ item.expiration_date }}
                    </span>
                </div>
            </div>
        </Card>

        <!-- Recent Items -->
        <Card v-if="summary && summary.recent_items.length > 0">
            <template #title>Recently Updated</template>
            <div class="space-y-2">
                <div
                    v-for="item in summary.recent_items"
                    :key="item.id"
                    class="flex justify-between items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded"
                >
                    <router-link
                        :to="`/inventory/${item.id}`"
                        class="text-indigo-600 dark:text-indigo-400 hover:underline"
                    >
                        {{ item.name }}
                    </router-link>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Qty: {{ item.quantity }}
                    </span>
                </div>
            </div>
        </Card>

        <!-- Token Management Card -->
        <Card>
            <template #title>Token Management</template>
            <!-- Create Token Form -->
            <div class="mb-8">
                <h2
                    class="text-xl font-semibold text-gray-800 dark:text-white mb-4"
                >
                    Create New Token
                </h2>
                <form
                    @submit.prevent="tokenStore.storeToken"
                    class="flex gap-4"
                >
                    <input
                        v-model="tokenStore.newToken.name"
                        type="text"
                        placeholder="Token name"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                    >
                        Create Token
                    </button>
                </form>
            </div>

            <!-- Tokens Table -->
            <div>
                <h2
                    class="text-xl font-semibold text-gray-800 dark:text-white mb-4"
                >
                    Your Tokens
                </h2>
                <div class="overflow-x-auto">
                    <table
                        class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Name
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Created At
                                </th>
                                <th
                                    scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                                >
                                    Last Used
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
                            class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                        >
                            <tr
                                v-for="token in tokenStore.tokens"
                                :key="token.id"
                            >
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white"
                                >
                                    {{ token.name }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ token.created_at }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400"
                                >
                                    {{ token.last_used_at || "Never" }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                >
                                    <button
                                        @click="
                                            tokenStore.destroyToken(token.id)
                                        "
                                        class="text-red-600 hover:text-red-900 focus:outline-none focus:underline dark:text-red-400 dark:hover:text-red-300"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="tokenStore.tokens.length === 0">
                                <td
                                    colspan="4"
                                    class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400"
                                >
                                    No tokens found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </Card>
    </Content>
</template>
