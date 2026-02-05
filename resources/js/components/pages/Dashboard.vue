<script setup>
import { onMounted } from "vue";
import { useTokenStore } from "@/stores/token";
import Card from "../common/Card.vue";
import Content from "../common/Content.vue";

const tokenStore = useTokenStore();

onMounted(() => {
    tokenStore.loadTokens();
});
</script>

<template>
    <Content>
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
