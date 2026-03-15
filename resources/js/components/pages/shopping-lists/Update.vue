<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import Content from "../../common/Content.vue";
import Card from "../../common/Card.vue";
import { useShoppingListStore } from "../../../stores/shopping-list";

const store = useShoppingListStore();
const router = useRouter();
const route = useRoute();

const listId = parseInt(route.params.id as string);

const form = ref({
    name: "",
    notes: "",
    shopping_date: "",
    is_completed: false,
});

onMounted(async () => {
    await store.loadList(listId);
    if (store.list) {
        form.value = {
            name: store.list.name,
            notes: store.list.notes,
            shopping_date: store.list.shopping_date || "",
            is_completed: store.list.is_completed,
        };
    }
});

const submitForm = async () => {
    if (!form.value.name.trim()) {
        alert("Please enter a shopping list name");
        return;
    }

    const success = await store.updateList(listId, {
        name: form.value.name,
        notes: form.value.notes,
        shopping_date: form.value.shopping_date || null,
        is_completed: form.value.is_completed,
    });

    if (success) {
        router.push(`/shopping-lists/${listId}`);
    }
};
</script>

<template>
    <Content>
        <Card>
            <template #title>Edit Shopping List</template>

            <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Shopping List Name *
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Notes
                    </label>
                    <textarea
                        v-model="form.notes"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        rows="3"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Shopping Date
                    </label>
                    <input
                        v-model="form.shopping_date"
                        type="date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                </div>

                <div class="flex items-center">
                    <input
                        v-model="form.is_completed"
                        type="checkbox"
                        id="is_completed"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600"
                    />
                    <label for="is_completed" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Mark as completed
                    </label>
                </div>

                <div class="flex gap-4">
                    <button
                        type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        Update Shopping List
                    </button>
                    <router-link
                        :to="`/shopping-lists/${listId}`"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md shadow-sm hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
                    >
                        Cancel
                    </router-link>
                </div>
            </form>
        </Card>
    </Content>
</template>
