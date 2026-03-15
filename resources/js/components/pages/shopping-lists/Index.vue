<script setup lang="ts">
import { onMounted } from "vue";
import { useRoute } from "vue-router";
import Content from "../../common/Content.vue";
import Card from "../../common/Card.vue";
import Paginator from "../../common/Paginator.vue";
import { useShoppingListStore } from "../../../stores/shopping-list";

const store = useShoppingListStore();
const route = useRoute();

const loadThisPage = () => {
    const page = route.query.page ? parseInt(route.query.page as string) : 1;
    store.loadLists(page);
};

onMounted(() => {
    loadThisPage();
});
</script>

<template>
    <Content>
        <Card>
            <template #title>Shopping Lists</template>
            <div class="mb-4">
                <router-link
                    to="/shopping-lists/create"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700"
                >
                    Create Shopping List
                </router-link>
            </div>
            <div class="overflow-x-auto">
                <table
                    class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                >
                    <thead class="bg-gray-50 dark:bg-gray-800">
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
                                Items
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                            >
                                Status
                            </th>
                            <th
                                scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                            >
                                Shopping Date
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
                        class="divide-y divide-gray-200 dark:divide-gray-700"
                    >
                        <tr
                            v-for="list in store.paginator.data"
                            :key="list.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ list.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ list.items_count ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span
                                    v-if="list.is_completed"
                                    class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold"
                                >
                                    Completed
                                </span>
                                <span
                                    v-else
                                    class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold"
                                >
                                    Active
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ list.shopping_date ?? "Not set" }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium gap-2 flex">
                                <router-link
                                    :to="`/shopping-lists/${list.id}`"
                                    class="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-400"
                                >
                                    View
                                </router-link>
                                <router-link
                                    :to="`/shopping-lists/${list.id}/edit`"
                                    class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400"
                                >
                                    Edit
                                </router-link>
                                <button
                                    @click="() => store.deleteList(list.id).then(() => loadThisPage())"
                                    class="text-red-600 hover:text-red-900 dark:hover:text-red-400"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Paginator :paginator="store.paginator" />
        </Card>
    </Content>
</template>
