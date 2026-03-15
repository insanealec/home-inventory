<script setup lang="ts">
import { computed } from "vue";
import { useShoppingListStore } from "../stores/shopping-list";
import { ShoppingListItem } from "../types/shopping-list";

interface Props {
    listId: number;
    items: ShoppingListItem[];
}

const props = defineProps<Props>();
const emit = defineEmits<{
    "update:items": [ShoppingListItem[]];
    "delete:item": [number];
}>();

const store = useShoppingListStore();

const updateItem = async (item: ShoppingListItem) => {
    await store.updateItem(props.listId, item.id, item);
};

const deleteItem = async (itemId: number) => {
    if (confirm("Are you sure you want to remove this item?")) {
        await store.deleteItem(props.listId, itemId);
        emit("delete:item", itemId);
    }
};

const toggleComplete = async (item: ShoppingListItem) => {
    item.is_completed = !item.is_completed;
    await updateItem(item);
};
</script>

<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-12"
                    >
                        Done
                    </th>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                        Item
                    </th>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                        Qty
                    </th>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                        Unit
                    </th>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                        Category
                    </th>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                        Est. Price
                    </th>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                        Notes
                    </th>
                    <th
                        scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                    >
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr
                    v-for="item in items"
                    :key="item.id"
                    class="hover:bg-gray-50 dark:hover:bg-gray-700"
                    :class="{ 'opacity-50 line-through': item.is_completed }"
                >
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input
                            type="checkbox"
                            :checked="item.is_completed"
                            @change="toggleComplete(item)"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        />
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ item.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ item.quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ item.unit || "—" }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ item.category?.name || "—" }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                        {{ item.estimated_price ? `$${item.estimated_price.toFixed(2)}` : "—" }}
                    </td>
                    <td class="px-6 py-4 max-w-xs truncate text-sm text-gray-700 dark:text-gray-300">
                        {{ item.notes || "—" }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button
                            @click="deleteItem(item.id)"
                            class="text-red-600 hover:text-red-900 dark:hover:text-red-400"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-if="items.length === 0" class="py-8 text-center text-gray-500 dark:text-gray-400">
            No items in this shopping list yet.
        </div>
    </div>
</template>
